<?php

return [

    // ── Navbar ──────────────────────────────────────────────────────────────
    'nav' => [
        'home'          => 'Home',
        'impact'        => 'Impact',
        'login'         => 'Login',
        'register'      => 'Register',
        'logout'        => 'Logout',
        'welcome'       => 'Welcome, :name',
        'sms_dashboard' => 'SMS Dashboard',
        'switch_lang'   => 'বাংলা',
    ],

    'impact' => [
        'page_title'       => 'Impact Dashboard — RuralConnect',
        'heading'          => 'Impact Dashboard',
        'subtitle'         => 'Tracking rural opportunity in real time: total jobs created, income received, and families supported.',
        'jobs_created'     => 'Jobs Created',
        'income_generated' => 'Income Generated',
        'families_supported' => 'Families Supported',
        'note'             => 'These public impact figures are generated from real tasks and payments logged on RuralConnect.',
        'cta'              => 'Join the Movement',
    ],

    // ── Home page ────────────────────────────────────────────────────────────
    'home' => [
        'tagline'  => 'RuralConnect',
        'subtitle' => 'Welcome to the Hyperlocal Micro-Task Economy for Rural Areas.',
    ],

    // ── Auth ─────────────────────────────────────────────────────────────────
    'auth' => [
        'page_login'     => 'Login — RuralConnect',
        'page_register'  => 'Register — RuralConnect',
        'login_heading'  => 'Welcome Back',
        'register_heading' => 'Join RuralConnect',
        'full_name'      => 'Full Name',
        'email'          => 'Email Address',
        'password'       => 'Password',
        'login_btn'      => 'Login',
        'create_btn'     => 'Create Account',
        'no_account'     => "Don't have an account?",
        'register_link'  => 'Register here',
        'name_placeholder'     => 'e.g. Rahim Uddin',
        'email_placeholder'    => 'name@example.com',
        'password_placeholder' => 'Min. 8 characters',
        'password_placeholder_login' => 'Enter your password',
        'mobile_number'        => 'Mobile Number (e.g. 017xxx)',
        'phone_placeholder'    => '017xxxxxxxx',
        'role_label'           => 'I want to',
        'role_worker'          => 'Seek Work (Worker)',
        'role_employer'        => 'Post Jobs (Employer)',
        'role_both'            => 'Both',
        'district_label'       => 'Home District',
        'district_placeholder' => 'e.g. Dhaka, Bogura',
        'upazila_label'        => 'Upazila / Area (Optional)',
        'upazila_placeholder'  => 'e.g. Shibganj',
        'skills_label'         => 'Primary Skills (Select all that apply)',
        'nid_label'            => 'National ID / Trade License (Optional for now)',
        'nid_placeholder'      => 'Enter NID for instant verification',
        'confirm_password'     => 'Confirm Password',
        'confirm_placeholder'  => 'Type password again',
        'sms_opt_in'           => 'Receive instant SMS job alerts & missed-call notifications',
        'login_id_label'       => 'Email Address or Mobile Number',
        'login_id_placeholder' => '017xxxxxxx or name@example.com',
        'remember_me'          => 'Remember me on this device',
    ],

    // ── Skills Categories (New Section for Badges & Matchmaking) ─────────────
    'skills' => [
        'farming'    => 'Farming',
        'harvesting' => 'Harvesting',
        'fishing'    => 'Fishing',
        'livestock'  => 'Livestock',
        'repair'     => 'Equipment Repair',
        'labor'      => 'Daily Labor',
    ],

    // ── SMS Dashboard ────────────────────────────────────────────────────────
    'sms' => [
        'page_title'       => 'SMS Dashboard — RuralConnect',
        'heading'          => 'SMS Dashboard',
        'refresh'          => 'Refresh',

        'stat_missed'      => 'Missed Calls',
        'stat_queued'      => 'SMS Queued',
        'stat_delivered'   => 'SMS Delivered',
        'stat_failed'      => 'SMS Failed',

        'sim_heading'      => 'Simulate a Missed Call',
        'sim_desc'         => 'Enter any Bangladesh phone number below to test the full flow — the system will look up the worker and SMS them matching jobs.',
        'caller_label'     => "Caller Number (worker's phone)",
        'called_label'     => 'Called Number (system DID)',
        'send_btn'         => 'Send Missed Call',

        'missed_log'       => 'Missed Calls Log',
        'sms_log'          => 'SMS Log',
        'no_missed'        => 'No missed calls yet. Use the simulator above.',
        'no_sms'           => 'No SMS sent yet.',

        'col_id'           => '#',
        'col_caller'       => 'Caller',
        'col_called'       => 'Called',
        'col_worker'       => 'Worker',
        'col_district'     => 'District',
        'col_jobs_sent'    => 'Jobs Sent',
        'col_status'       => 'Status',
        'col_time'         => 'Time',
        'col_to'           => 'To',
        'col_message'      => 'Message',
        'col_gateway'      => 'Gateway',
        'col_attempts'     => 'Attempts',
        'col_sent_at'      => 'Sent At',

        'unregistered'     => 'Unregistered',
    ],
];