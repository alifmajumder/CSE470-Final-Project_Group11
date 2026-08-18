<?php

return [

    // ── Navbar ──────────────────────────────────────────────────────────────
    'nav' => [
        'home'          => 'হোম',
        'impact'        => 'ইমপ্যাক্ট',
        'login'         => 'লগইন',
        'register'      => 'নিবন্ধন',
        'logout'        => 'লগআউট',
        'welcome'       => ':name, স্বাগতম',
        'sms_dashboard' => 'এসএমএস ড্যাশবোর্ড',
        'switch_lang'   => 'English',
    ],

    'impact' => [
        'page_title'       => 'ইমপ্যাক্ট ড্যাশবোর্ড — রুরালকানেক্ট',
        'heading'          => 'ইমপ্যাক্ট ড্যাশবোর্ড',
        'subtitle'         => 'গ্রামীণ অর্থনীতির বাস্তব প্রভাব দেখুন: তৈরি কাজ, আয় এবং সহায়তাকৃত পরিবার।',
        'jobs_created'     => 'তৈরি কাজ',
        'income_generated' => 'উত্পন্ন আয়',
        'families_supported' => 'সহায়তাকৃত পরিবার',
        'note'             => 'এই গণিতগত তথ্য RuralConnect-এ লগ করা প্রকৃত কাজ এবং পেমেন্ট থেকে তৈরি হয়।',
        'cta'              => 'এখানে যোগ দিন',
    ],

    // ── Home page ────────────────────────────────────────────────────────────
    'home' => [
        'tagline'  => 'রুরালকানেক্ট',
        'subtitle' => 'গ্রামীণ এলাকার জন্য স্থানীয় কর্মসংস্থান প্ল্যাটফর্মে আপনাকে স্বাগতম।',
    ],

    // ── Auth ─────────────────────────────────────────────────────────────────
    'auth' => [
        'page_login'     => 'লগইন — রুরালকানেক্ট',
        'page_register'  => 'নিবন্ধন — রুরালকানেক্ট',
        'login_heading'  => 'আবার স্বাগতম',
        'register_heading' => 'রুরালকানেক্টে যোগ দিন',
        'full_name'      => 'পুরো নাম',
        'email'          => 'ইমেইল ঠিকানা',
        'password'       => 'পাসওয়ার্ড',
        'login_btn'      => 'লগইন',
        'create_btn'     => 'অ্যাকাউন্ট তৈরি করুন',
        'no_account'     => 'অ্যাকাউন্ট নেই?',
        'register_link'  => 'এখানে নিবন্ধন করুন',
        'name_placeholder'     => 'যেমন: রহিম উদ্দিন',
        'email_placeholder'    => 'name@example.com',
        'password_placeholder' => 'কমপক্ষে ৮ অক্ষর',
        'password_placeholder_login' => 'আপনার পাসওয়ার্ড দিন',
        'mobile_number'        => 'মোবাইল নম্বর (যেমন: 017xxx)',
        'phone_placeholder'    => '017xxxxxxxx',
        'role_label'           => 'আমি চাই',
        'role_worker'          => 'কাজ খুঁজতে (শ্রমিক)',
        'role_employer'        => 'কাজ দিতে (নিয়োগকর্তা)',
        'role_both'            => 'উভয়ই',
        'district_label'       => 'নিজ জেলা',
        'district_placeholder' => 'যেমন: ঢাকা, বগুড়া',
        'upazila_label'        => 'উপজেলা / এলাকা (ঐচ্ছিক)',
        'upazila_placeholder'  => 'যেমন: শিবগঞ্জ',
        'skills_label'         => 'প্রাথমিক দক্ষতা (প্রযোজ্য সবগুলো বেছে নিন)',
        'nid_label'            => 'জাতীয় পরিচয়পত্র / ট্রেড লাইসেন্স (আপাতত ঐচ্ছিক)',
        'nid_placeholder'      => 'তাৎক্ষণিক ভেরিফিকেশনের জন্য এনআইডি দিন',
        'confirm_password'     => 'পাসওয়ার্ড নিশ্চিত করুন',
        'confirm_placeholder'  => 'পাসওয়ার্ডটি আবার লিখুন',
        'sms_opt_in'           => 'তাৎক্ষণিক এসএমএস কাজের অ্যালার্ট এবং মিসড-কল নোটিফিকেশন পান',
        'login_id_label'       => 'ইমেইল বা মোবাইল নম্বর',
        'login_id_placeholder' => '017xxxxxxx বা name@example.com',
        'remember_me'          => 'এই ডিভাইসে মনে রাখুন',
    ],

    // ── Skills Categories (New Section for Badges & Matchmaking) ─────────────
    'skills' => [
        'farming'    => 'কৃষি কাজ',
        'harvesting' => 'ফসল কাটা',
        'fishing'    => 'মাছ ধরা',
        'livestock'  => 'গবাদি পশু পালন',
        'repair'     => 'যন্ত্রপাতি মেরামত',
        'labor'      => 'দৈনিক শ্রম',
    ],

    // ── SMS Dashboard ────────────────────────────────────────────────────────
    'sms' => [
        'page_title'       => 'এসএমএস ড্যাশবোর্ড — রুরালকানেক্ট',
        'heading'          => 'এসএমএস ড্যাশবোর্ড',
        'refresh'          => 'রিফ্রেশ',

        'stat_missed'      => 'মিসড কল',
        'stat_queued'      => 'এসএমএস সারিবদ্ধ',
        'stat_delivered'   => 'এসএমএস পৌঁছেছে',
        'stat_failed'      => 'এসএমএস ব্যর্থ',

        'sim_heading'      => 'মিসড কল সিমুলেট করুন',
        'sim_desc'         => 'নিচে যেকোনো বাংলাদেশি ফোন নম্বর দিন — সিস্টেম কর্মীকে চিহ্নিত করে কাছের কাজের এসএমএস পাঠাবে।',
        'caller_label'     => 'কলারের নম্বর (কর্মীর ফোন)',
        'called_label'     => 'সিস্টেম নম্বর (DID)',
        'send_btn'         => 'মিসড কল পাঠান',

        'missed_log'       => 'মিসড কল লগ',
        'sms_log'          => 'এসএমএস লগ',
        'no_missed'        => 'এখনো কোনো মিসড কল নেই। উপরের সিমুলেটর ব্যবহার করুন।',
        'no_sms'           => 'এখনো কোনো এসএমএস পাঠানো হয়নি।',

        'col_id'           => '#',
        'col_caller'       => 'কলার',
        'col_called'       => 'কল করা হয়েছে',
        'col_worker'       => 'কর্মী',
        'col_district'     => 'জেলা',
        'col_jobs_sent'    => 'কাজ পাঠানো',
        'col_status'       => 'অবস্থা',
        'col_time'         => 'সময়',
        'col_to'           => 'প্রাপক',
        'col_message'      => 'বার্তা',
        'col_gateway'      => 'গেটওয়ে',
        'col_attempts'     => 'চেষ্টা',
        'col_sent_at'      => 'পাঠানো সময়',

        'unregistered'     => 'অনিবন্ধিত',
    ],
];