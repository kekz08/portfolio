<?php

return [
    'meta_title' => 'Vingie Nimez | Portfolio',
    'next_chapter_label' => 'Next Chapter',
    'sections' => [
        ['id' => 'top', 'number' => '01', 'label' => 'Top'],
        ['id' => 'about_me', 'number' => '02', 'label' => 'About Me'],
        ['id' => 'services', 'number' => '03', 'label' => 'My Services'],
        ['id' => 'attainments', 'number' => '04', 'label' => 'Skills'],
        ['id' => 'fun_facts', 'number' => '05', 'label' => 'Facts'],
        ['id' => 'experience', 'number' => '06', 'label' => 'Experiences'],
        ['id' => 'projects', 'number' => '07', 'label' => 'Projects'],
        ['id' => 'certificates', 'number' => '08', 'label' => 'Certificates'],
        ['id' => 'how_i_work', 'number' => '09', 'label' => 'How I Work'],
        ['id' => 'blogs', 'number' => '10', 'label' => 'Blogs'],
        ['id' => 'testimonials', 'number' => '11', 'label' => 'Highlights'],
        ['id' => 'contact', 'number' => '12', 'label' => 'Contact'],
    ],
    'navbar' => [
        'view_work_label' => 'View Work',
        'view_work_target' => 'projects',
        'hire_me_label' => 'Hire Me',
        'hire_me_href' => '/register',
    ],
    'sidebar' => [
        'contact_email' => 'vingienimez@gmail.com',
        'contact_phone' => '+63 915 827 9204',
    ],
    'hero' => [
        'tagline' => 'Hi, I am an IT student and aspiring developer',
        'typing_words' => ['Full-Stack Developer', 'Laravel and Vue.js Builder', 'System Support Specialist'],
        'location' => 'based in San Francisco, Anao-aon, Surigao del Norte.',
        'view_work_label' => 'View My Work',
        'view_work_target' => 'projects',
        'hire_me_label' => 'Hire Me',
        'hire_me_href' => '/register',
        'image' => '/legacy/images/hero.png',
        'image_alt' => 'Vingie Nimez',
    ],
    'about' => [
        'background_label' => 'About Me',
        'intro_label' => 'Professional Summary',
        'title' => 'Profile Overview',
        'headline' => "I'm Vingie O. Nimez, a BSIT student focused on practical web systems",
        'description' => 'Motivated and goal-driven Information Technology student with a solid foundation in Laravel and Vue.js development. I focus on building efficient, user-centered web applications and applying technical and analytical skills to projects that support innovation and organizational growth.',
        'email' => 'vingienimez@gmail.com',
        'date_of_birth' => '-',
        'location' => 'San Francisco, Anao-aon, Surigao del Norte',
        'experience_years' => '11',
        'experience_label' => 'Years of work experience',
    ],
    'services' => [
        'label' => '03 My Services',
        'title' => 'What I Deliver',
        'cards' => [
            [
                'title' => 'Web and Mobile Application Development',
                'items' => ['Laravel Backend Development', 'Vue.js Web Interface Development', 'React Native Mobile App Development', 'REST API Integration and Business Logic'],
            ],
            [
                'title' => 'Operations and Deployment',
                'items' => ['VPS Deployment (Linux / LAMP Stack)', 'cPanel Deployment and Domain Setup', 'Environment Configuration, SSL, and Database Migration', 'Production Monitoring and Maintenance Support'],
            ],
            [
                'title' => 'IT Data and System Support',
                'items' => ['Technical Documentation and Workflow Mapping', 'Data Management and System Record Organization', 'User Setup and Access Support', 'Web System Troubleshooting and Maintenance'],
            ],
        ],
    ],
    'skills' => [
        'label' => '04 Skills',
        'title' => 'Core IT Capabilities',
        'items' => [
            ['title' => 'Laravel Backend Development', 'value' => 80],
            ['title' => 'Vue.js Web Interface Development', 'value' => 78],
            ['title' => 'React Native Mobile Development', 'value' => 72],
            ['title' => 'VPS Deployment (LAMP / cPanel)', 'value' => 76],
            ['title' => 'IT System Support and Troubleshooting', 'value' => 88],
        ],
    ],
    'facts' => [
        'label' => '05 Facts',
        'title' => 'Quick Facts',
        'items' => [
            ['value' => '2', 'label' => 'Major Projects Built'],
            ['value' => '3', 'label' => 'Work Roles Completed'],
            ['value' => '11', 'label' => 'Years of Work Experience'],
            ['value' => '2022', 'label' => 'BSIT Start Year'],
        ],
    ],
    'experience' => [
        'label' => '06 Experiences',
        'title' => 'Career and Education Timeline',
        'timeline' => [
            [
                'period' => '2022 - Present',
                'title' => 'Bachelor of Science in Information Technology',
                'company' => 'Surigao del Norte State University',
                'desc' => 'Currently pursuing BSIT with focus on practical web development, system design, and technical problem-solving.',
            ],
            [
                'period' => '2018 - 2021',
                'title' => 'Encoder / Driver',
                'company' => 'Saint Francis Xavier Parish',
                'desc' => 'Managed data encoding and records while supporting transport-related operational needs.',
            ],
            [
                'period' => '2012 - 2017',
                'title' => 'CCTV Operator',
                'company' => 'THPAL',
                'desc' => 'Monitored surveillance systems and maintained daily security reports and observations.',
            ],
            [
                'period' => '2010 - 2012',
                'title' => 'Foreman',
                'company' => 'Skippy Stevedoring Services',
                'desc' => 'Supervised operations and workforce coordination while maintaining safety and efficiency.',
            ],
            [
                'period' => '1997 - 2001',
                'title' => 'Secondary Education',
                'company' => 'San Nicolas High School',
                'desc' => 'Completed secondary education and built foundational academic and teamwork skills.',
            ],
            [
                'period' => '1991 - 1997',
                'title' => 'Elementary Education',
                'company' => 'Anao-aon Central Elementary School',
                'desc' => 'Completed primary education and built early learning fundamentals.',
            ],
        ],
    ],
    'projects' => [
        'label' => '07 Projects',
        'title' => 'Resume-Based Projects',
        'items' => [
            [
                'image' => '/legacy/projects/sdms/sdms_web.png',
                'title' => 'Scholarship Data Management System',
                'category' => 'Laravel + Vue.js',
                'summary' => 'Developed a web-based system for scholarship records, user management, and application tracking using Laravel and Vue.js.',
            ],
            [
                'image' => '/legacy/projects/pinoypark_mobile/pinoypark.png',
                'title' => 'PinoyPark Community Platform',
                'category' => 'Laravel + Vue.js',
                'summary' => 'Designed and built a dynamic WAP platform with real-time interactions, automated workflows, and responsive user interfaces.',
            ],
            [
                'image' => '/legacy/projects/streaming_sites/streaming_01.png',
                'title' => 'Streaming Sites Showcase',
                'category' => 'Frontend Build',
                'summary' => 'Created media-focused website layouts with responsive sections, improved browsing flow, and clear visual hierarchy.',
            ],
        ],
    ],
    'certificates' => [
        'label' => '08 Certificates',
        'title' => 'Professional Certifications',
        'items' => [
            [
                'title' => 'cPanel and WHM System Administrator I',
                'issuer' => 'cPanel University',
                'image' => '/legacy/certificates/vingie_nimez_cPanel amp; WHM System Administrator I Certification Exam-1.png',
                'file' => '/legacy/certificates/vingie_nimez_cPanel &amp; WHM System Administrator I Certification Exam.pdf',
            ],
            [
                'title' => 'cPanel and WHM System Administrator II',
                'issuer' => 'cPanel University',
                'image' => '/legacy/certificates/vingie_nimez_cPanel amp; WHM System Administrator II Certification Exam-1.png',
                'file' => '/legacy/certificates/vingie_nimez_cPanel &amp; WHM System Administrator II Certification Exam.pdf',
            ],
            [
                'title' => 'cPanel Professional Certification Exam',
                'issuer' => 'cPanel University',
                'image' => '/legacy/certificates/vingie_nimez_cPanel Professional Certification Exam-1.png',
                'file' => '/legacy/certificates/vingie_nimez_cPanel Professional Certification Exam.pdf',
            ],
            [
                'title' => 'WHM Administration Certification Exam',
                'issuer' => 'cPanel University',
                'image' => '/legacy/certificates/vingie_nimez_WHM Administration Certification Exam-1.png',
                'file' => '/legacy/certificates/vingie_nimez_WHM Administration Certification Exam .pdf',
            ],
        ],
    ],
    'process' => [
        'label' => '09 How I Work',
        'title' => 'Working Approach',
        'steps' => [
            [
                'step' => 'Step 01',
                'title' => 'Gather Requirements',
                'description' => 'Clarify goals, users, and expected outcomes to define a practical technical direction.',
            ],
            [
                'step' => 'Step 02',
                'title' => 'Plan the Build',
                'description' => 'Map workflows, data structure, and key screens before coding starts.',
            ],
            [
                'step' => 'Step 03',
                'title' => 'Develop and Validate',
                'description' => 'Implement with Laravel and Vue.js, then validate usability and core functionality.',
            ],
            [
                'step' => 'Step 04',
                'title' => 'Deliver and Refine',
                'description' => 'Deliver the solution, gather feedback, and improve performance and user experience.',
            ],
        ],
    ],
    'blogs' => [
        'label' => '10 Case Notes',
        'title' => 'Project Snapshots',
        'cards' => [
            [
                'image' => '/legacy/projects/sdms/sdms_mobile.jpg',
                'title' => 'Building the Scholarship Data Management System',
                'date' => 'Case Note',
                'time' => 'Laravel + Vue.js',
                'summary' => 'Focused on scholarship record workflows, user access handling, and application processing features.',
            ],
            [
                'image' => '/legacy/projects/pinoypark_mobile/pinoypark.png',
                'title' => 'Engineering the PinoyPark Community Platform',
                'date' => 'Case Note',
                'time' => 'Dynamic Platform Build',
                'summary' => 'Highlights real-time interactions, automated workflows, and responsive front-end implementation.',
            ],
            [
                'image' => '/legacy/projects/streaming_sites/streaming_01.png',
                'title' => 'Delivering a Streaming Site Experience',
                'date' => 'Case Note',
                'time' => 'Media Platform Development',
                'summary' => 'Focused on modern streaming UI, content browsing flow, and responsive playback-oriented page design.',
            ],
        ],
    ],
    'testimonials' => [
        'label' => '11 Highlights',
        'title' => 'Professional Value',
        'items' => [
            [
                'quote' => 'Combines field operations experience with modern web development training, enabling practical and reliable system delivery.',
                'author' => 'Core Strength',
                'role' => 'Professional Positioning',
            ],
            [
                'quote' => 'Focused on user-centered applications using Laravel and Vue.js, supported by strong documentation and technical support discipline.',
                'author' => 'Work Style',
                'role' => 'Professional Positioning',
            ],
        ],
    ],
    'contact' => [
        'label' => '12 Contact',
        'title' => "Let's Collaborate",
        'phone' => '+63 915 827 9204',
        'email' => 'vingienimez@gmail.com',
        'location' => 'San Francisco, Anao-aon, Surigao del Norte',
        'description' => 'Open to junior developer roles, freelance web projects, and technical support opportunities where I can contribute Laravel and Vue.js skills with dependable execution.',
        'hire_me_label' => 'Hire Me',
        'hire_me_href' => '/register',
        'form_title' => 'Leave a Message',
        'submit_label' => 'Send Message',
        'success_message' => 'Message captured on frontend. Backend integration will be connected next.',
    ],
    'footer' => [
        'brand' => 'Vingie Nimez',
        'tagline' => 'BSIT Portfolio | Laravel + Vue.js',
    ],
];
