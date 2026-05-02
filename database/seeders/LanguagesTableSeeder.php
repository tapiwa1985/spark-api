<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            // ============================================
            // SOUTH AFRICAN OFFICIAL LANGUAGES (11)
            // ============================================
            [
                'language_name' => 'English',
                'native_name' => 'English',
                'code' => 'en',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Afrikaans',
                'native_name' => 'Afrikaans',
                'code' => 'af',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'isiZulu',
                'native_name' => 'isiZulu',
                'code' => 'zu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'isiXhosa',
                'native_name' => 'isiXhosa',
                'code' => 'xh',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Sepedi (Northern Sotho)',
                'native_name' => 'Sepedi',
                'code' => 'nso',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Sesotho (Southern Sotho)',
                'native_name' => 'Sesotho',
                'code' => 'st',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Setswana',
                'native_name' => 'Setswana',
                'code' => 'tn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Xitsonga',
                'native_name' => 'Xitsonga',
                'code' => 'ts',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Siswati',
                'native_name' => 'Siswati',
                'code' => 'ss',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Tshivenda',
                'native_name' => 'Tshivenda',
                'code' => 've',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'isiNdebele',
                'native_name' => 'isiNdebele',
                'code' => 'nr',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ============================================
            // SOUTHERN AFRICA (Beyond SA Borders)
            // ============================================
            [
                'language_name' => 'Shona',
                'native_name' => 'chiShona',
                'code' => 'sn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Ndebele (Zimbabwe)',
                'native_name' => 'isiNdebele',
                'code' => 'nd',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Chichewa (Nyanja)',
                'native_name' => 'Chichewa',
                'code' => 'ny',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Tumbuka',
                'native_name' => 'chiTumbuka',
                'code' => 'tum',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Lozi',
                'native_name' => 'Silozi',
                'code' => 'loz',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Bemba',
                'native_name' => 'chiBemba',
                'code' => 'bem',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Herero',
                'native_name' => 'Otjiherero',
                'code' => 'hz',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Kwanyama (Ovambo)',
                'native_name' => 'Oshikwanyama',
                'code' => 'kj',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Ndonga (Ovambo)',
                'native_name' => 'Oshindonga',
                'code' => 'ng',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ============================================
            // EAST AFRICA
            // ============================================
            [
                'language_name' => 'Swahili',
                'native_name' => 'Kiswahili',
                'code' => 'sw',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Luganda',
                'native_name' => 'Luganda',
                'code' => 'lg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Kinyarwanda',
                'native_name' => 'Ikinyarwanda',
                'code' => 'rw',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Kirundi',
                'native_name' => 'Ikirundi',
                'code' => 'rn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Gikuyu (Kikuyu)',
                'native_name' => 'Gĩkũyũ',
                'code' => 'ki',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Luo (Dholuo)',
                'native_name' => 'Dholuo',
                'code' => 'luo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Luhya',
                'native_name' => 'Luhya',
                'code' => 'luy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Kalenjin',
                'native_name' => 'Kalenjin',
                'code' => 'kln',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Somali',
                'native_name' => 'Soomaaliga',
                'code' => 'so',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Oromo',
                'native_name' => 'Afaan Oromoo',
                'code' => 'om',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Amharic',
                'native_name' => 'አማርኛ',
                'code' => 'am',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Tigrinya',
                'native_name' => 'ትግርኛ',
                'code' => 'ti',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Afar',
                'native_name' => 'Qafar',
                'code' => 'aa',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ============================================
            // WEST AFRICA
            // ============================================
            [
                'language_name' => 'Hausa',
                'native_name' => 'Hausa',
                'code' => 'ha',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Yoruba',
                'native_name' => 'Yorùbá',
                'code' => 'yo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Igbo',
                'native_name' => 'Igbo',
                'code' => 'ig',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Fula (Fulfulde)',
                'native_name' => 'Fulfulde',
                'code' => 'ff',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Akan (Twi)',
                'native_name' => 'Akan',
                'code' => 'ak',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Ewe',
                'native_name' => 'Eʋegbe',
                'code' => 'ee',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Ga',
                'native_name' => 'Gã',
                'code' => 'gaa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Bambara',
                'native_name' => 'Bamanankan',
                'code' => 'bm',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Wolof',
                'native_name' => 'Wolof',
                'code' => 'wo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Serer',
                'native_name' => 'Serer',
                'code' => 'srr',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Mandinka',
                'native_name' => 'Mandinka',
                'code' => 'mnk',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Susu',
                'native_name' => 'Sosoxui',
                'code' => 'sus',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Mossi (Moore)',
                'native_name' => 'Mòoré',
                'code' => 'mos',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ============================================
            // CENTRAL AFRICA
            // ============================================
            [
                'language_name' => 'Lingala',
                'native_name' => 'Lingála',
                'code' => 'ln',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Kikongo',
                'native_name' => 'Kikongo',
                'code' => 'kg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Tshiluba',
                'native_name' => 'Tshiluba',
                'code' => 'lua',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Sango',
                'native_name' => 'Sängö',
                'code' => 'sg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Kituba',
                'native_name' => 'Kituba',
                'code' => 'ktu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Ganda',
                'native_name' => 'Luganda',
                'code' => 'lg',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ============================================
            // NORTH AFRICA
            // ============================================
            [
                'language_name' => 'Arabic',
                'native_name' => 'العربية',
                'code' => 'ar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Berber (Tamazight)',
                'native_name' => 'ⵜⴰⵎⴰⵣⵉⵖⵜ',
                'code' => 'ber',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Egyptian Arabic',
                'native_name' => 'اللهجة المصرية',
                'code' => 'arz',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Moroccan Arabic (Darija)',
                'native_name' => 'الداريجة',
                'code' => 'ary',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Hassaniya Arabic',
                'native_name' => 'حسانية',
                'code' => 'mey',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ============================================
            // MAJOR INTERNATIONAL LANGUAGES (Expat Professionals)
            // ============================================
            [
                'language_name' => 'French',
                'native_name' => 'Français',
                'code' => 'fr',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'German',
                'native_name' => 'Deutsch',
                'code' => 'de',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Portuguese',
                'native_name' => 'Português',
                'code' => 'pt',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Spanish',
                'native_name' => 'Español',
                'code' => 'es',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Italian',
                'native_name' => 'Italiano',
                'code' => 'it',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Dutch',
                'native_name' => 'Nederlands',
                'code' => 'nl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Mandarin Chinese',
                'native_name' => '普通话',
                'code' => 'zh',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Hindi',
                'native_name' => 'हिन्दी',
                'code' => 'hi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Urdu',
                'native_name' => 'اردو',
                'code' => 'ur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Bengali',
                'native_name' => 'বাংলা',
                'code' => 'bn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Punjabi',
                'native_name' => 'ਪੰਜਾਬੀ',
                'code' => 'pa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Tamil',
                'native_name' => 'தமிழ்',
                'code' => 'ta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Telugu',
                'native_name' => 'తెలుగు',
                'code' => 'te',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Gujarati',
                'native_name' => 'ગુજરાતી',
                'code' => 'gu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Malayalam',
                'native_name' => 'മലയാളം',
                'code' => 'ml',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Kannada',
                'native_name' => 'ಕನ್ನಡ',
                'code' => 'kn',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Nepali',
                'native_name' => 'नेपाली',
                'code' => 'ne',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Sinhala',
                'native_name' => 'සිංහල',
                'code' => 'si',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Greek',
                'native_name' => 'Ελληνικά',
                'code' => 'el',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Turkish',
                'native_name' => 'Türkçe',
                'code' => 'tr',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Russian',
                'native_name' => 'Русский',
                'code' => 'ru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Polish',
                'native_name' => 'Polski',
                'code' => 'pl',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Czech',
                'native_name' => 'Čeština',
                'code' => 'cs',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Hungarian',
                'native_name' => 'Magyar',
                'code' => 'hu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Romanian',
                'native_name' => 'Română',
                'code' => 'ro',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Bulgarian',
                'native_name' => 'Български',
                'code' => 'bg',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Serbian',
                'native_name' => 'Српски',
                'code' => 'sr',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Croatian',
                'native_name' => 'Hrvatski',
                'code' => 'hr',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Swedish',
                'native_name' => 'Svenska',
                'code' => 'sv',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Danish',
                'native_name' => 'Dansk',
                'code' => 'da',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Norwegian',
                'native_name' => 'Norsk',
                'code' => 'no',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Finnish',
                'native_name' => 'Suomi',
                'code' => 'fi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Thai',
                'native_name' => 'ภาษาไทย',
                'code' => 'th',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Vietnamese',
                'native_name' => 'Tiếng Việt',
                'code' => 'vi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Khmer (Cambodian)',
                'native_name' => 'ភាសាខ្មែរ',
                'code' => 'km',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Korean',
                'native_name' => '한국어',
                'code' => 'ko',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Japanese',
                'native_name' => '日本語',
                'code' => 'ja',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Hebrew',
                'native_name' => 'עברית',
                'code' => 'he',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'language_name' => 'Persian (Farsi)',
                'native_name' => 'فارسی',
                'code' => 'fa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert languages, skipping duplicates if they already exist
        foreach ($languages as $language) {
            DB::table('languages')->updateOrInsert(
                ['code' => $language['code']],
                $language
            );
        }
    }
}