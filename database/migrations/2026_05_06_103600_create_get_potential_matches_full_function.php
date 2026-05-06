<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
CREATE OR REPLACE FUNCTION get_potential_matches_full(
    p_current_user_id BIGINT,
    p_current_lat DOUBLE PRECISION,
    p_current_lng DOUBLE PRECISION,
    p_max_distance_km NUMERIC DEFAULT 50,
    p_limit INTEGER DEFAULT 50
)
RETURNS TABLE (
    match_score NUMERIC,
    distance_meters DOUBLE PRECISION,
    shared_interests INTEGER,
    shared_languages INTEGER,
    same_industry INTEGER,
    user_id BIGINT,
    name VARCHAR,
    email VARCHAR,
    profile_id BIGINT,
    bio TEXT,
    dob DATE,
    gender VARCHAR,
    job_title VARCHAR,
    industry_id BIGINT,
    location JSONB,
    industry JSONB,
    images JSONB,
    interests JSONB,
    languages JSONB
)
LANGUAGE plpgsql
AS $$
BEGIN
    RETURN QUERY
    WITH me AS (
        SELECT
            u.id AS user_id,
            up.id AS profile_id,
            up.industry_id
        FROM users u
        JOIN user_profiles up ON up.user_id = u.id
        WHERE u.id = p_current_user_id
    ),
    my_interests AS (
        SELECT iup.interest_id
        FROM interest_user_profile iup
        JOIN me ON me.profile_id = iup.user_profile_id
    ),
    my_languages AS (
        SELECT lup.language_id
        FROM language_user_profile lup
        JOIN me ON me.profile_id = lup.user_profile_id
    ),
    candidates AS (
        SELECT
            up.id AS profile_id,
            up.user_id,
            ST_Distance(
                up.location::geography,
                ST_SetSRID(ST_MakePoint(p_current_lng, p_current_lat), 4326)::geography
            ) AS distance_meters
        FROM user_profiles up
        JOIN me ON TRUE
        WHERE up.user_id <> me.user_id
          AND up.location IS NOT NULL
          AND ST_DWithin(
                up.location::geography,
                ST_SetSRID(ST_MakePoint(p_current_lng, p_current_lat), 4326)::geography,
                p_max_distance_km * 1000
          )
          AND NOT EXISTS (
              SELECT 1
              FROM likes l
              WHERE l.user_id = me.user_id
                AND l.liked_user_id = up.user_id
          )
          AND NOT EXISTS (
              SELECT 1
              FROM user_matches um
              WHERE (
                    (um.user_id = me.user_id AND um.matched_user_id = up.user_id)
                 OR (um.user_id = up.user_id AND um.matched_user_id = me.user_id)
              )
              AND um.status = 'ACTIVE'
          )
    ),
    scored AS (
        SELECT
            c.profile_id,
            c.user_id,
            c.distance_meters,
            COALESCE(si.shared_interests, 0) AS shared_interests,
            COALESCE(sl.shared_languages, 0) AS shared_languages,
            CASE WHEN up.industry_id = me.industry_id THEN 1 ELSE 0 END AS same_industry,
            (
                (COALESCE(si.shared_interests, 0) * 3.0) +
                (COALESCE(sl.shared_languages, 0) * 2.0) +
                (CASE WHEN up.industry_id = me.industry_id THEN 1.5 ELSE 0 END) -
                (c.distance_meters / 1000.0 * 0.15)
            ) AS match_score
        FROM candidates c
        JOIN user_profiles up ON up.id = c.profile_id
        JOIN me ON TRUE
        LEFT JOIN LATERAL (
            SELECT COUNT(*)::int AS shared_interests
            FROM interest_user_profile ci
            WHERE ci.user_profile_id = c.profile_id
              AND ci.interest_id IN (SELECT interest_id FROM my_interests)
        ) si ON TRUE
        LEFT JOIN LATERAL (
            SELECT COUNT(*)::int AS shared_languages
            FROM language_user_profile cl
            WHERE cl.user_profile_id = c.profile_id
              AND cl.language_id IN (SELECT language_id FROM my_languages)
        ) sl ON TRUE
    )
    SELECT
        s.match_score,
        s.distance_meters,
        s.shared_interests,
        s.shared_languages,
        s.same_industry,

        u.id AS user_id,
        u.name,
        u.email,

        up.id AS profile_id,
        up.bio,
        up.dob,
        up.gender,
        up.job_title,
        up.industry_id,
        ST_AsGeoJSON(up.location)::jsonb AS location,

        jsonb_build_object(
            'id', i.id,
            'industry_name', i.industry_name
        ) AS industry,

        COALESCE(img.images, '[]'::jsonb) AS images,
        COALESCE(ints.interests, '[]'::jsonb) AS interests,
        COALESCE(langs.languages, '[]'::jsonb) AS languages

    FROM scored s
    JOIN user_profiles up ON up.id = s.profile_id
    JOIN users u ON u.id = up.user_id
    LEFT JOIN industries i ON i.id = up.industry_id

    LEFT JOIN LATERAL (
        SELECT jsonb_agg(
            jsonb_build_object(
                'id', pi.id,
                'url', pi.url,
                'is_display_picture', pi.is_display_picture,
                'created_at', pi.created_at
            )
            ORDER BY pi.is_display_picture DESC, pi.id ASC
        ) AS images
        FROM profile_images pi
        WHERE pi.user_profile_id = up.id
    ) img ON TRUE

    LEFT JOIN LATERAL (
        SELECT jsonb_agg(
            DISTINCT jsonb_build_object(
                'id', it.id,
                'interest_name', it.interest_name,
                'interest_category_id', it.interest_category_id
            )
        ) AS interests
        FROM interest_user_profile iup
        JOIN interests it ON it.id = iup.interest_id
        WHERE iup.user_profile_id = up.id
    ) ints ON TRUE

    LEFT JOIN LATERAL (
        SELECT jsonb_agg(
            DISTINCT jsonb_build_object(
                'id', l.id,
                'name', l.name
            )
        ) AS languages
        FROM language_user_profile lup
        JOIN languages l ON l.id = lup.language_id
        WHERE lup.user_profile_id = up.id
    ) langs ON TRUE

    ORDER BY s.match_score DESC, s.distance_meters ASC
    LIMIT p_limit;
END;
$$;
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared(<<<'SQL'
DROP FUNCTION IF EXISTS get_potential_matches_full(
    BIGINT,
    DOUBLE PRECISION,
    DOUBLE PRECISION,
    NUMERIC,
    INTEGER
);
SQL);
    }
};
