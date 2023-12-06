<?php

use Corals\Foundation\Search\IndexedRecord;
use Corals\Menu\Models\Menu;
use Corals\Modules\CMS\Models\Block;
use Corals\Modules\CMS\Models\Page;
use Corals\Modules\CMS\Models\Post;
use Corals\Modules\CMS\Models\Widget;
use Corals\Modules\Payment\Common\Models\Invoice;
use Corals\Modules\Reservation\database\seeds\ReservationCategoriesSeeder;
use Corals\Modules\Reservation\Facades\Invoices;
use Corals\Modules\Reservation\Models\LineItem;
use Corals\Modules\Reservation\Models\Rate;
use Corals\Modules\Reservation\Models\Reservation;
use Corals\Modules\Reservation\Models\Service;
use Corals\Settings\Models\CustomFieldSetting;
use Corals\User\Models\Role;
use Corals\User\Models\User;
use Corals\Utility\Category\Models\Category;
use Corals\Utility\Rating\Models\Rating;
use Corals\Utility\Schedule\Classes\ScheduleManager;
use Corals\Utility\Schedule\Models\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;


if (!function_exists('clear_theme_data_before_seed')) {
    /**
     *
     */
    function clear_theme_data_before_seed(): void
    {
        Service::query()->delete();

        Reservation::query()->delete();

        Rate::query()->delete();

        Invoice::query()->delete();


        LineItem::query()->delete();

        Schedule::query()->delete();

        Category::query()->where('module', 'Reservation')->delete();

        $doctorRole = Role::query()
            ->where('name', 'doctor')
            ->first();

        $patientRole = Role::query()
            ->where('name', 'member')
            ->first();


        if ($doctorRole) {
            $doctorRole->users()->delete();
            $doctorRole->delete();
        }

        if ($patientRole) {
            $patientRole->users()->delete();
//            $patientRole->delete();
        }

        Rating::query()->delete();

        IndexedRecord::query()
            ->where('fulltext_search.indexable_type', getMorphAlias(Service::class))
            ->delete();

        CustomFieldSetting::query()->where('model', getMorphAlias(Service::class))->delete();

        Artisan::call('db:seed', [
            '--class' => ReservationCategoriesSeeder::class
        ]);
    }
}

if (!function_exists('create_res')) {
    /**
     * @param $service
     * @param $patientId
     * @param $dateStartObject
     * @param $createdAtObject
     * @param $lineItems
     * @param string $status
     */
    function create_res($service, $patientId, $dateStartObject, $createdAtObject, $lineItems, $status = 'completed')
    {
        $user = User::find($patientId);

        $reservation = Reservation::query()
            ->create([
                'code' => Reservation::getCode('RES'),
                'service_id' => $service->id,
                'object_id' => $service->id,
                'owner_type' => 'User',
                'owner_id' => $patientId,
                'status' => $status,
                'starts_at' => $dateStartObject->copy()->setMinutes(0)->toDateTimeString(),
                'ends_at' => $dateStartObject->copy()->addMinutes($service->slot_in_minutes)->toDateTimeString(),
                'object_type' => 'ResService',
                'created_at' => $createdAtObject,
                'properties' => [
                    'contact_details' => [
                        'first_name' => $user->name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'phone' => $user->phone_number
                    ]
                ]
            ]);

        $invoice = Invoices::generate($reservation, $lineItems);

        $invoice->update([
            'created_at' => $reservation->created_at,
            'due_date' => $reservation->created_at,
            'status' => $reservation->status
        ]);

    }
}

clear_theme_data_before_seed();
//Doctor Role
$doctorRole = Role::create([
    'name' => 'doctor',
    'label' => 'Doctor',
    'guard_name' => config('auth.defaults.guard'),
    'subscription_required' => 0,
    'created_at' => now(),
    'updated_at' => now(),
    'dashboard_theme' => 'reservation-doctors',
    'dashboard_url' => 'my-dashboard'

]);

$patientRole = Role::where(['name' => 'member'])->first();


$patientRole->update([
    'dashboard_theme' => 'reservation-doctors',
    'dashboard_url' => 'my-dashboard'
]);

$serviceParentCategory = Category::query()->where('name', 'services_categories')->first();

$categoryIds = [];

$sidebarMenu = Menu::query()
    ->firstOrCreate(['key' => 'doctors_sidebar'], [
        'name' => 'Doctors Sidebar',
        'parent_id' => 0,
        'url' => null,
        'description' => 'Doctors Menu Item',
        'icon' => null,
        'target' => null,
        'roles' => [1],
        'order' => 0
    ]);


Menu::query()->where('parent_id', $sidebarMenu->id)->delete();

$sidebarMenus = [
    [
        'name' => 'Dashboard',
        'icon' => 'fas fa-columns',
        'url' => 'my-dashboard',
        'description' => 'Dashboard',
        'active_menu_url' => 'my-dashboard*',
        'roles' => [1, $patientRole->id, $doctorRole->id]
    ],
    [
        'name' => 'My Patients',
        'icon' => 'fas fa-user-injured',
        'url' => 'my-patients',
        'description' => 'My Patients Menu Items',
        'active_menu_url' => 'my-patients*',
        'roles' => [$doctorRole->id]

    ],
    [
        'name' => 'Line Items',
        'icon' => 'fas fa-compass',
        'url' => 'doctor/line-items',
        'description' => 'Doctor Line items Menu Items',
        'active_menu_url' => 'doctor/line-items*',
        'roles' => [$doctorRole->id]
    ],
    [
        'name' => 'Edit My Service',
        'icon' => 'fas fa-hourglass-start',
        'url' => 'doctor/my-service',
        'description' => 'Doctor Service',
        'active_menu_url' => 'doctor/my-service*',
        'roles' => [$doctorRole->id]
    ],
    [
        'name' => 'Favourites',
        'icon' => 'fas fa-bookmark',
        'url' => 'favourites',
        'description' => 'Favourites Menu Items',
        'active_menu_url' => 'favourites',
        'roles' => [1, $patientRole->id, $doctorRole->id]

    ],
    [
        'name' => 'Message',
        'icon' => 'fas fa-comments',
        'url' => 'messaging/discussions',
        'description' => 'Message Menu Items',
        'active_menu_url' => 'messaging/discussions*',
        'roles' => [1, $patientRole->id, $doctorRole->id]

    ],
    [
        'name' => 'Create Support Ticket',
        'icon' => 'fas fa-ticket-alt',
        'url' => 'trouble-ticket/public/create',
        'description' => 'Support ticket Items',
        'active_menu_url' => 'trouble-ticket/public/create',
        'roles' => [1, $patientRole->id, $doctorRole->id]
    ],
    [
        'name' => 'Profile Settings',
        'icon' => 'fas fa-user-cog',
        'url' => 'profile',
        'description' => 'Profile Settings Menu Items',
        'active_menu_url' => 'profile',
        'roles' => [1, $patientRole->id, $doctorRole->id]
    ],
];


foreach ($sidebarMenus as $menu) {
    Menu::query()->create([
        'name' => $menu['name'],
        'icon' => $menu['icon'],
        'description' => $menu['description'],
        'url' => $menu['url'],
        'status' => 'active',

        'parent_id' => $sidebarMenu->id,
        'active_menu_url' => $menu['active_menu_url'],
        'roles' => $menu['roles']
    ]);
}


foreach (['Dentist', 'Cardiology', 'Urology', 'Orthopaedics'] as $categoryName) {
    $categories[] = Category::query()->create([
        'parent_id' => $serviceParentCategory->id,
        'name' => $categoryName,
        'slug' => Str::slug($categoryName),
        'description' => "$categoryName Service Category",
        'module' => 'Reservation',
        'status' => 'active',
        'properties' => [
            'thumbnail_link' => "/assets/themes/reservation-doctors/img/specialities/" . strtolower($categoryName) . ".png",
        ]
    ]);
}

CustomFieldSetting::query()->create([
    'model' => getMorphAlias(Service::class),
    'fields' => [
        [
            "name" => "address",
            "type" => "text",
            "status" => "active",
            "label" => "Address",
            "default_value" => null,
            "validation_rules" => "required",
            'field_config' => [
                "grid_class" => null,
                "order" => null
            ],
            "custom_attributes" => [
                [
                    "key" => "id",
                    "value" => "_autocomplete"
                ],
            ],
            'options_setting' => [
                "source" => null,
                "source_model" => null,
                "source_model_column" => null,
            ]
        ],

        [
            "name" => "lat",
            "type" => "hidden",
            "status" => "active",
            "label" => "lat",
            "default_value" => null,
            "validation_rules" => "required",
            'field_config' => [
                "grid_class" => null,
                "order" => null
            ],
            "custom_attributes" => [
                [
                    "key" => "id",
                    "value" => "lat"
                ],
            ],
            'options_setting' => [
                "source" => null,
                "source_model" => null,
                "source_model_column" => null,
            ]
        ],
        [
            "name" => "long",
            "type" => "hidden",
            "status" => "active",
            "label" => "long",
            "default_value" => null,
            "validation_rules" => "required",
            'field_config' => [
                "grid_class" => null,
                "order" => null
            ],
            "custom_attributes" => [
                [
                    "key" => "id",
                    "value" => "long"
                ],
            ],
            'options_setting' => [
                "source" => null,
                "source_model" => null,
                "source_model_column" => null,
            ]
        ],

    ]
]);


//Assign Permissions here
$doctorRole->givePermissionTo([
    //reservation permissions
    'Reservation::reservation.view',
    'Reservation::reservation.create',
    'Reservation::reservation.update',
    //service permissions
    'Reservation::service.view',
    'Reservation::service.update',
    //line item permissions
    'Reservation::line_item.view',
    'Reservation::line_item.create',
    'Reservation::line_item.update',
    'Reservation::line_item.delete',
    //rate permissions
    'Reservation::rate.view',
    'Reservation::rate.create',
    'Reservation::rate.update',
    'Reservation::rate.delete',

    //messaging permissions
    'Messaging::discussion.view',
    'Messaging::discussion.create',
    'Messaging::discussion.update',
    'Messaging::discussion.delete',
    'Messaging::discussion.select_recipient',

    'Messaging::message.view',
    'Messaging::message.create',
    'Messaging::message.update',
    'Messaging::message.delete',


    //service
    'Reservation::service.create',

    //rating
    'Utility::rating.create',
    'Utility::rating.view',
    'Utility::rating.update',

    //invoices
    'Payment::invoices.view'
]);

$patientRole->givePermissionTo([
    //reservation permissions
    'Reservation::reservation.view',
    'Reservation::reservation.create',
    'Reservation::reservation.update',

    //messaging permissions
    'Messaging::discussion.view',
    'Messaging::discussion.create',
    'Messaging::discussion.update',
    'Messaging::discussion.delete',
    'Messaging::discussion.select_recipient',

    'Messaging::message.view',
    'Messaging::message.create',
    'Messaging::message.update',
    'Messaging::message.delete',

    //rating
    'Utility::rating.create',
    'Utility::rating.view',
    'Utility::rating.update',

    //invoices
    'Payment::invoices.view'

]);

$faker = app()->make(Faker\Generator::class);


tap(User::updateOrCreate(['email' => 'patient@corals.io'], [
    'name' => 'Patient',
    'last_name' => 'Member',
    'email' => 'patient@corals.io',
    'password' => '123456',
]), function ($patientMember) use ($patientRole) {
    $patientMember->assignRole($patientRole);
});

$addresses = [
    [
        'address' => 'New Deal, TX, America',
        'lat' => 33.73730520000001,
        'long' => -101.8365585
    ],
    [
        'address' => 'New York New York Casino',
        'lat' => 36.1023786,
        'long' => -115.1745465
    ],
    [
        'address' => 'East Columbus Street, Columbus, OH, USA ',
        'lat' => 39.9462506,
        'long' => -82.9733854
    ],

    [
        'address' => 'West Chicago Avenue, Chicago, IL, USA',
        'lat' => 41.8956094,
        'long' => -87.7016721
    ],
    [
        'address' => 'Nevada 439, Sparks, NV, USA',
        'lat' => 39.4712493,
        'long' => -119.393079
    ],

    [
        'address' => 'Nevada 439, Sparks, NV, USA',
        'lat' => 39.4712493,
        'long' => -119.393079
    ],
    [
        'address' => '439 Nevada Street, Reno, NV, USA',
        'lat' => 39.5295662,
        'long' => -119.8201017
    ],
    [
        'address' => 'Chicago Street, Cary, IL, USA',
        'lat' => 42.2021939,
        'long' => -88.2326929
    ],

    [
        'address' => 'DC Village Lane Southwest, D.C., DC, USA',
        'lat' => 38.817805,
        'long' => -77.01335999999999
    ],
    [
        'address' => 'Dupont Circle, Fort Lesley J. McNair, Washington D.C., DC, USA',
        'lat' => 38.9096936,
        'long' => -77.043339
    ],

];
$patientIds = [];

foreach (range(1, 25) as $j) {
    $firstName = $faker->firstName;
    $lastName = $faker->lastName;

    $patientData = [
        'name' => $firstName,
        'last_name' => $lastName,
        'email' => sprintf("%s.%s@%s", $firstName, $lastName, $faker->safeEmailDomain),
        'password' => '123456',
    ];

    $patient = tap(User::query()->create($patientData), function ($patient) use ($patientRole) {
        $patient->assignRole($patientRole);
    });

    $patientIds[] = $patient->id;
}
$doctorMemberCreated = false;

foreach (range(1, 20) as $i) {
    if (!$doctorMemberCreated) {
        $firstName = 'Doctor';
        $lastName = 'Member';
        $email = 'Doctor@corals.io';
        $doctorMemberCreated = true;
    } else {
        $firstName = $faker->firstName;
        $lastName = $faker->lastName;
        $email = sprintf("%s.%s@%s", $firstName, $lastName, $faker->safeEmailDomain);
    }
    $doctorData = [
        'name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'password' => '123456',
        'phone_number' => $faker->e164PhoneNumber
    ];

    $doctor = User::query()->create($doctorData);
    $doctor->assignRole($doctorRole);

    $mainLineItems = [
        'Normal Visit',
        'Consulting Visit'
    ];

    $doctorCategory = $categories[array_rand($categories)];

    $lineItemsDef = [
        'Orthopaedics' => [
            'Bone deformities',
            'Bone infections',
            'Bone tumors',
            'Fractures',
            'Spinal deformities'
        ],
        'Dentist' => [
            'Dental Fillings',
            'Whitening',
            'Crowns, bridges',
            'full and partial dentures',
            'Fillings, root canals, and extractions'
        ],
        'Cardiology' => [
            'Arrhythmia',
            'Continuing cardiac care',
            'Cardiac rehabilitation',
            'Male cardiovascular health clinic',
            'Valve disease',
            'Structural intervention'
        ],
        'Urology' => [
            'Prostate biopsy',
            'Treatment of erectile dysfunction',
            'Pediatric Urological Surgery',
        ]
    ];

    $lineItems = [];


    //create main line items

    $mainLineItem = $mainLineItems[array_rand($mainLineItems)];

    $mainLineItemObject = LineItem::query()->create([
        'code' => sprintf("%s", Str::slug($mainLineItem, '_')),
        'name' => $mainLineItem,
        'owner_type' => 'User',
        'owner_id' => $doctor->id,
        'rate_type' => 'fixed',
        'rate_value' => rand(50, 150),
        'status' => 'active',
        'description' => $mainLineItem,
    ]);


    //create optional line items
    foreach ($lineItemsDef[$doctorCategory->name] as $lineItemName) {
        $lineItem = LineItem::query()->create([
            'code' => Str::snake(str_replace(',', '', $lineItemName)),
            'name' => $lineItemName,
            'owner_type' => 'User',
            'owner_id' => $doctor->id,
            'rate_type' => 'fixed',
            'rate_value' => rand(50, 450),
            'status' => 'active',
            'description' => $lineItemName,
        ]);

        $lineItems[] = $lineItem->id;
    }

    $timeSlots = [30, 45, 60, 90];


    $address = $addresses[rand(0, 9)];
    $service = Service::query()->create([
        'code' => Service::getCode('SER'),
        'name' => $doctor->full_name . ' Appointment Service',
        'slot_in_minutes' => $timeSlots[$faker->randomKey($timeSlots)],
        'status' => 'active',
        'object_type' => null,
        'owner_type' => 'User',
        'owner_id' => $doctor->id,
        'caption' => $faker->realText(60),
        'description' => $faker->realText(450),
        'properties' => [
            "address" => data_get($address, 'address'),
            "long" => data_get($address, 'long'),
            "lat" => data_get($address, 'lat'),
        ]

    ]);

    foreach (range(rand(1, 3), rand(4, 6)) as $k) {
        Rating::create([
            'rating' => rand(3, 5),
            'title' => $faker->sentence(10),
            'body' => $faker->paragraph(5),
            'reviewrateable_type' => getMorphAlias(Service::class),
            'reviewrateable_id' => $service->id,
            'author_type' => getMorphAlias(User::class),
            'author_id' => $patientIds[rand(0, count($patientIds) - 1)],
            'status' => 'approved'
        ]);
    }

    $scheduleManager = new ScheduleManager($service);

    $service->categories()->sync($doctorCategory->id);

    $schedule = array(
        'Mon' =>
            array(
                'start' => '08',
                'end' => '17',
            ),
        'Tue' =>
            array(
                'start' => '08',
                'end' => '17',
            ),
        'Wed' =>
            array(
                'start' => '08',
                'end' => '17',
            ),
        'Thu' =>
            array(
                'start' => '08',
                'end' => '17',
            ),
        'Fri' =>
            array(
                'start' => '08',
                'end' => '17',
            ),
        'Sat' =>
            array(
                'start' => 'Off',
                'end' => 'Off',
            ),
        'Sun' =>
            array(
                'start' => 'Off',
                'end' => 'Off',
            ),
    );

    $scheduleManager->createSchedule($schedule, $doctor);


    DB::table('res_model_has_line_items')->insert([
        'line_item_id' => $mainLineItemObject->id,
        'model_type' => getMorphAlias($service),
        'model_id' => $service->id,
        'is_main_line_item' => true
    ]);

    foreach ($lineItems as $lineItemId) {
        DB::table('res_model_has_line_items')->insert([
            'line_item_id' => $lineItemId,
            'model_type' => getMorphAlias($service),
            'model_id' => $service->id,
            'is_main_line_item' => false
        ]);
    }


    create_res($service, $patientIds[rand(0, count($patientIds) - 1)], now()->subDays($r = rand(2, 9)),
        today()->subMonths(rand(1, 4))->startOfDay(), [
            [
                'code' => $mainLineItemObject->code,
                'name' => $mainLineItemObject->name,
                'rate_value' => $mainLineItemObject->rate_value,
                'notes' => null,
            ]
        ]);

    create_res($service, $patientIds[rand(0, count($patientIds) - 1)], now()->subDays($r = rand(2, 6)),
        today()->subMonths(rand(1, 4))->startOfDay(), [
            [
                'code' => $mainLineItemObject->code,
                'name' => $mainLineItemObject->name,
                'rate_value' => $mainLineItemObject->rate_value,
                'notes' => null,
            ]
        ]);


    create_res($service, $patientIds[rand(0, count($patientIds) - 1)], now()->subDays($r = rand(1, 6)),
        today()->subDays($r)->startOfDay(), [
            [
                'code' => $mainLineItemObject->code,
                'name' => $mainLineItemObject->name,
                'rate_value' => $mainLineItemObject->rate_value,
                'notes' => null,
            ]
        ]);

    if ($faker->randomDigit % 2) {
        create_res($service, $patientIds[rand(0, count($patientIds) - 1)], now()->subDays($r = rand(3, 5)),
            today()->subDays($r)->startOfDay(), [
                [
                    'code' => $mainLineItemObject->code,
                    'name' => $mainLineItemObject->name,
                    'rate_value' => $mainLineItemObject->rate_value,
                    'notes' => null,
                ]
            ], 'pending');
    }

    if ($faker->randomDigit % 2) {
        create_res($service, $patientIds[rand(0, count($patientIds) - 1)], now()->subDays($r = rand(1, 7)),
            today()->subDays($r)->startOfDay(), [
                [
                    'code' => $mainLineItemObject->code,
                    'name' => $mainLineItemObject->name,
                    'rate_value' => $mainLineItemObject->rate_value,
                    'notes' => null,
                ]
            ], 'draft');
    }
    if ($faker->randomDigit % 2) {
        create_res($service, $patientIds[rand(0, count($patientIds) - 1)], now()->subDays($r = rand(1, 7)),
            today()->subDays($r)->startOfDay(), [
                [
                    'code' => $mainLineItemObject->code,
                    'name' => $mainLineItemObject->name,
                    'rate_value' => $mainLineItemObject->rate_value,
                    'notes' => null,
                ]
            ], 'cancelled');
    }


    $service->indexRecord();
}

Invoice::query()->update(['status' => 'paid']);

$role = Role::findByName('superuser');
$role->syncPermissions(Permission::all());

$categories = [];

$posts = [];

if (Schema::hasTable('posts')
    && class_exists(Page::class)
    && class_exists(Post::class)
) {
    Page::updateOrCreate(['slug' => 'home', 'type' => 'page',],
        array(
            'title' => 'Home',
            'slug' => 'home',
            'meta_keywords' => 'home',
            'meta_description' => 'home',
            'content' => null,
            'published' => 1,
            'published_at' => '2017-11-16 14:26:52',
            'private' => 0,
            'type' => 'page',
            'template' => 'home',
            'author_id' => 1,
            'deleted_at' => null,
            'created_at' => '2017-11-16 16:27:04',
            'updated_at' => '2017-11-16 16:27:07',
        ));
    Page::updateOrCreate(['slug' => 'blog', 'type' => 'page'],
        array(
            'title' => 'Blog',
            'slug' => 'blog',
            'meta_keywords' => 'Blog',
            'meta_description' => 'Blog',
            'content' => '<div class="text-center">
<h2>Blog</h2>

<p class="lead">Pellentesque habitant morbi tristique senectus et netus et malesuada</p>
</div>',
            'published' => 1,
            'published_at' => '2017-11-16 11:56:34',
            'private' => 0,
            'type' => 'page',
            'template' => 'full',
            'author_id' => 1,
            'deleted_at' => null,
            'created_at' => '2017-11-16 11:56:34',
            'updated_at' => '2017-11-16 11:56:34',
        ));

    Page::updateOrCreate(['slug' => 'contact-us', 'type' => 'page'],
        array(
            'title' => 'Contact Us',
            'slug' => 'contact-us',
            'meta_keywords' => 'Contact Us',
            'meta_description' => 'Contact Us',
            'content' => '<div><h2 style="text-align: center;">Drop Your Message</h2><p class="lead" style="text-align: center;">Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p></div>',
            'published' => 1,
            'published_at' => '2017-11-16 11:56:34',
            'private' => 0,
            'type' => 'page',
            'template' => 'contact',
            'author_id' => 1,
            'deleted_at' => null,
            'created_at' => '2017-11-16 11:56:34',
            'updated_at' => '2017-11-16 11:56:34',
        ));

    $posts[] = Post::updateOrCreate([
        'slug' => 'subscription-commerce-trends-for-2018',
        'type' => 'post'
    ],
        array(
            'title' => 'Subscription Commerce Trends for 2018',
            'meta_keywords' => null,
            'meta_description' => null,
            'content' => '<p>Subscription commerce is ever evolving. A few years ago, who would have expected&nbsp;<a href="https://techcrunch.com/2017/10/10/porsche-launches-on-demand-subscription-for-its-sports-cars-and-suvs/" target="_blank">Porsche</a>&nbsp;to launch a subscription service? Or that monthly boxes of beauty samples or shaving supplies and&nbsp;<a href="https://www.pymnts.com/subscription-commerce/2017/how-over-the-top-services-came-out-on-top/" target="_blank">OTT services</a>&nbsp;would propel the subscription model to new heights? And how will these trends shape the subscription space going forward&mdash;and drive growth and innovation?</p>

<p>Regardless of your billing model, there&rsquo;s an opportunity for you to capitalize on many of the current trends in subscription commerce&mdash;trends that will help you to continue to compete and succeed in your industry.</p>

<h3><strong>What are these trends and how can you learn more?</strong></h3>

<p>These trends are outlined in our &ldquo;Top Ten Trends for 2018&rdquo; which we publish every year to help subscription businesses understand the drivers which may impact them in 2018 and beyond.</p>

<p>One trend, for example, is machine learning and data science which the payments industry is increasingly utilizing to deliver more powerful results for their customers.</p>

<p>Another trend which is driving new revenue is the adoption of a hybrid billing model&mdash; subscription businesses seamlessly sell one-time items and &lsquo;traditional&rsquo; businesses add a subscription component as a means to introduce a new revenue stream.</p>

<p>And while subscriber acquisition is not a new trend, there are some sophisticated ways to acquire new customers that subscription businesses are putting to work for increasingly positive effect.</p>

<p>Download this year&rsquo;s edition and see how these trends and insights can help your subscription business succeed in 2018.</p>

<p>&nbsp;</p>',
            'published' => 1,
            'published_at' => '2017-12-04 11:18:23',
            'private' => 0,
            'type' => 'post',
            'template' => null,
            'author_id' => 1,
            'deleted_at' => null,
            'created_at' => '2017-12-03 23:45:51',
            'updated_at' => '2017-12-04 13:18:23',
        ));
    $posts[] = Post::updateOrCreate([
        'slug' => 'using-machine-learning-to-optimize-subscription-billing',
        'type' => 'post'
    ],
        array(
            'title' => 'Using Machine Learning to Optimize Subscription Billing',
            'meta_keywords' => null,
            'meta_description' => null,
            'content' => '<p>As a data scientist at Recurly, my job is to use the vast amount of data that we have collected to build products that make subscription businesses more successful. One way to think about data science at Recurly is as an extended R&amp;D department for our customers. We use a variety of tools and techniques, attack problems big and small, but at the end of the day, our goal is to put all of Recurly&rsquo;s expertise to work in service of your business.</p>

<p>Managing a successful subscription business requires a wide range of decisions. What is the optimum structure for subscription plans and pricing? What are the most effective subscriber acquisition methods? What are the most efficient collection methods for delinquent subscribers? What strategies will reduce churn and increase revenue?</p>

<p>At Recurly, we&#39;re focused on building the most flexible subscription management platform, a platform that provides a competitive advantage for your business. We reduce the complexity of subscription billing so you can focus on winning new subscribers and delighting current subscribers.</p>

<p>Recently, we turned to data science to tackle a big problem for subscription businesses: involuntary churn.</p>

<h3><strong>The Problem: The Retry Schedule</strong></h3>

<p>One of the most important factors in subscription commerce is subscriber retention. Every billing event needs to occur flawlessly to avoid adversely impacting the subscriber relationship or worse yet, to lose that subscriber to churn.</p>

<p>Every time a subscription comes up for renewal, Recurly creates an invoice and initiates a transaction using the customer&rsquo;s stored billing information, typically a credit card. Sometimes, this transaction is declined by the payment processor or the customer&rsquo;s bank. When this happens, Recurly sends reminder emails to the customer, checks with the Account Updater service to see if the customer&#39;s card has been updated, and also attempts to collect payment at various intervals over a period of time defined by the subscription business. The timing of these collection attempts is called the &ldquo;retry schedule.&rdquo;</p>

<p>Our ability to correct and successfully retry these cards prevents lost revenue, positively impacts your bottom line, and increases your customer retention rate.</p>

<p>Other subscription providers typically offer a static, one-size-fits-all retry schedule, or leave the schedule up to the subscription business, without providing any guidance. In contrast, Recurly can use machine learning to craft a retry schedule that is tailored to each individual invoice based on our historical data with hundreds of millions of transactions. Our approach gives each invoice the best chance of success, without any manual work by our customers.</p>

<p>A key component of Recurly&rsquo;s values is to test, learn and iterate. How did we call on those values to build this critical component of the Recurly platform?</p>

<h3><strong>Applying Machine Learning</strong></h3>

<p>We decided to use statistical models that leverage Recurly&rsquo;s data on transactions (hundreds of millions of transactions built up over years from a wide variety of different businesses) to predict which transactions are likely to succeed. Then, we used these models to craft the ideal retry schedule for each individual invoice. The process of building the models is known as machine learning.</p>

<p>The term &quot;machine learning&quot; encompasses many different processes and methods, but at its heart is an effort to go past explicitly programmed logic and allow a computer to arrive at the best logic on its own.</p>

<p>While humans are optimized for learning certain tasks&mdash;like how children can speak a new language after simply listening for a few months&mdash;computers can also be trained to learn patterns. Aggregating hundreds of millions of transactions to look for the patterns that lead to transaction success is a classic machine learning problem.</p>

<p>A typical machine learning project involves gathering data, training a statistical model on that data, and then evaluating the performance of the model when presented with new data. A model is only as good as the data it&rsquo;s trained on, and here we had a huge advantage.</p>',
            'published' => 1,
            'published_at' => '2017-12-04 11:21:25',
            'private' => 0,
            'type' => 'post',
            'template' => null,
            'author_id' => 1,
            'deleted_at' => null,
            'created_at' => '2017-12-04 13:21:25',
            'updated_at' => '2017-12-04 13:21:25',
        ));
    $posts[] = Post::updateOrCreate([
        'slug' => 'why-you-need-a-blog-subscription-landing-page',
        'type' => 'post'
    ],
        array(
            'title' => 'Why You Need A Blog Subscription Landing Page',
            'meta_keywords' => null,
            'meta_description' => null,
            'content' => '<p>Whether subscribing via email or RSS, your site visitor is individually volunteering to add your content to their day; a day that is already crowded with content from emails, texts, voicemails, site content, and even snail mail. &nbsp;</p>

<p>As a business, each time you receive a new blog subscriber, you have received validation or &quot;a vote&quot; that your audience has identified YOUR content as adding value to their day. With each new blog subscriber, your content is essentially being awarded as being highly relevant to conversations your readers are having on a regular basis.&nbsp;</p>

<p>To best promote the content your blog subscribers can expect to receive on an ongoing basis,&nbsp;<strong>consider adding a blog subscription landing page.&nbsp;</strong>This is a quick win that will help your company enhance the blogging subscription experience and help you measure and manage the success of this offer with analytical insight.</p>

<p>Holistically, your goal with this landing page is to provide visitors with a sneak preview of the experience they will receive by becoming a blog subscriber.<strong>&nbsp;Your blog subscription landing page should include:</strong></p>

<ul>
<li><strong>A high-level overview of topics, categories your blog will discuss.&nbsp;&nbsp;</strong>For example, HubSpot&#39;s blog covers &quot;all of the inbound marketing - SEO, Blogging, Social Media, Landing Pages, Lead Generation, and Analytics.&quot;</li>
<li><strong>Insight into &quot;who&quot; your blog will benefit.&nbsp;&nbsp;</strong>Examples may include HR Directors, Financial Business professionals, Animal Enthusiasts, etc.&nbsp; If your blog appeals to multiple personas, feel free to spell this out.&nbsp; This will help assure your visitor that they are joining a group of like-minded individuals who share their interests and goals.&nbsp;&nbsp;</li>
<li><strong>How your blog will help to drive the relevant conversation.&nbsp;</strong>Examples may include &quot;updates on industry events&quot;, &quot;expert editorials&quot;, &quot;insider tips&quot;, etc.&nbsp;&nbsp;</li>
</ul>

<p><strong>To create your blog subscription landing page, consider the following steps:</strong></p>

<p>1) Create your landing page following&nbsp;landing page best practices.&nbsp; Consider the &quot;subscribing to your blog&quot; offer as similar to other offers you promote using Landing Pages.&nbsp;</p>

<p>2) Create a Call To Action button that will link to this landing page.&nbsp; Use this button as a call to action within your blog articles or on other website pages to link to a blog subscription landing page&nbsp;Make sure your CTA button is supercharged!</p>

<p>3)&nbsp;Create a Thank You Page&nbsp;to complete the sign-up experience with gratitude and a follow-up call to action.&nbsp;</p>

<p>4) Measure the success of your blog subscription landing page.&nbsp;Consider the 3 Secrets to Optimizing Landing Page Copy.&nbsp;</p>

<p>For more information on Blogging Success Strategies,&nbsp;check out more Content Camp Resources and recorded webinars.&nbsp;</p>',
            'published' => 1,
            'published_at' => '2017-12-04 11:33:19',
            'private' => 0,
            'type' => 'post',
            'template' => null,
            'author_id' => 1,
            'deleted_at' => null,
            'created_at' => '2017-12-04 13:31:46',
            'updated_at' => '2017-12-04 13:33:19',
        ));
}

if (Schema::hasTable('categories') && class_exists(\Corals\Modules\CMS\Models\Category::class)) {
    $categories[] = \Corals\Modules\CMS\Models\Category::updateOrCreate([
        'name' => 'Computers',
        'slug' => 'computers',
    ]);
    $categories[] = \Corals\Modules\CMS\Models\Category::updateOrCreate([
        'name' => 'Smartphone',
        'slug' => 'smartphone',
    ]);
    $categories[] = \Corals\Modules\CMS\Models\Category::updateOrCreate([
        'name' => 'Gadgets',
        'slug' => 'gadgets',
    ]);
    $categories[] = \Corals\Modules\CMS\Models\Category::updateOrCreate([
        'name' => 'Technology',
        'slug' => 'technology',
    ]);
    $categories[] = \Corals\Modules\CMS\Models\Category::updateOrCreate([
        'name' => 'Engineer',
        'slug' => 'engineer',
    ]);
    $categories[] = \Corals\Modules\CMS\Models\Category::updateOrCreate([
        'name' => 'Subscriptions',
        'slug' => 'subscriptions',
    ]);
    $categories[] = \Corals\Modules\CMS\Models\Category::updateOrCreate([
        'name' => 'Billing',
        'slug' => 'billing',
    ]);
}

$posts_media = [
    0 => array(
        'id' => 4,
        'model_type' => 'Corals\\Modules\\CMS\\Models\\Post',
        'collection_name' => 'featured-image',
        'name' => 'subscription_trends',
        'file_name' => 'subscription_trends.png',
        'mime_type' => 'image/png',
        'disk' => 'media',
        'size' => 20486,
        'manipulations' => '[]',
        'custom_properties' => '{"root":"demo"}',
        'order_column' => 6,
        'created_at' => '2017-12-03 23:45:51',
        'updated_at' => '2017-12-03 23:45:51',
    ),
    1 => array(
        'id' => 8,
        'model_type' => 'Corals\\Modules\\CMS\\Models\\Post',
        'collection_name' => 'featured-image',
        'name' => 'machine_learning',
        'file_name' => 'machine_learning.png',
        'mime_type' => 'image/png',
        'disk' => 'media',
        'size' => 32994,
        'manipulations' => '[]',
        'custom_properties' => '{"root":"demo"}',
        'order_column' => 11,
        'created_at' => '2017-12-04 13:21:25',
        'updated_at' => '2017-12-04 13:21:25',
    ),
    2 => array(
        'id' => 9,
        'model_type' => 'Corals\\Modules\\CMS\\Models\\Post',
        'collection_name' => 'featured-image',
        'name' => 'Successful-Blog_Fotolia_102410353_Subscription_Monthly_M',
        'file_name' => 'Successful-Blog_Fotolia_102410353_Subscription_Monthly_M.jpg',
        'mime_type' => 'image/jpeg',
        'disk' => 'media',
        'size' => 182317,
        'manipulations' => '[]',
        'custom_properties' => '{"root":"demo"}',
        'order_column' => 12,
        'created_at' => '2017-12-04 13:33:19',
        'updated_at' => '2017-12-04 13:33:19',
    ),
];

foreach ($posts as $index => $post) {
    $randIndex = rand(0, 6);
    if (isset($categories[$randIndex])) {
        $category = $categories[$randIndex];
        try {
            \DB::table('category_post')->insert(array(
                array(
                    'post_id' => $post->id,
                    'category_id' => $category->id,
                )
            ));
        } catch (Exception $exception) {
        }
    }

    if (isset($posts_media[$index])) {
        try {
            $posts_media[$index]['model_id'] = $post->id;
            \DB::table('media')->insert($posts_media[$index]);
        } catch (Exception $exception) {
        }
    }
}

if (class_exists(Menu::class) && Schema::hasTable('posts')) {
    // seed root menus
    $topMenu = Corals\Menu\Models\Menu::updateOrCreate(['key' => 'frontend_top'], [
        'parent_id' => 0,
        'url' => null,
        'name' => 'Frontend Top',
        'description' => 'Frontend Top Menu',
        'icon' => null,
        'target' => null,
        'order' => 0
    ]);

    $topMenuId = $topMenu->id;

    Menu::query()->where('parent_id', $topMenu->id)->delete();

    // seed children menu
    Corals\Menu\Models\Menu::updateOrCreate(['key' => 'home'], [
        'parent_id' => $topMenuId,
        'url' => '/',
        'active_menu_url' => '/',
        'name' => 'Home',
        'description' => 'Home Menu Item',
        'icon' => 'fa fa-home',
        'target' => null,
        'order' => 0
    ]);

    Corals\Menu\Models\Menu::updateOrCreate([
        'parent_id' => $topMenuId,
        'key' => null,
        'url' => 'reserve/list',
        'active_menu_url' => 'reserve/list',
        'name' => 'Doctors',
        'description' => 'Doctors Menu Item',
        'icon' => null,
        'target' => null,
        'order' => 980
    ]);


    Corals\Menu\Models\Menu::updateOrCreate([
        'parent_id' => $topMenuId,
        'key' => null,
        'url' => 'blog',
        'active_menu_url' => 'blog',
        'name' => 'Blog',
        'description' => 'Blog Menu Item',
        'icon' => null,
        'target' => null,
        'order' => 980
    ]);

    Corals\Menu\Models\Menu::updateOrCreate([
        'parent_id' => $topMenuId,
        'key' => null,
        'url' => 'contact-us',
        'active_menu_url' => 'contact-us',
        'name' => 'Contact Us',
        'description' => 'Contact Us Menu Item',
        'icon' => null,
        'target' => null,
        'order' => 980
    ]);

    $footerMenu = Corals\Menu\Models\Menu::updateOrCreate(['key' => 'frontend_footer'], [
        'parent_id' => 0,
        'url' => null,
        'name' => 'Frontend Footer',
        'description' => 'Frontend Footer Menu',
        'icon' => null,
        'target' => null,
        'order' => 0
    ]);

    $footerMenuId = $footerMenu->id;

// seed children menu
    Corals\Menu\Models\Menu::updateOrCreate(['key' => 'footer_home'], [
        'parent_id' => $footerMenuId,
        'url' => '/',
        'active_menu_url' => '/',
        'name' => 'Home',
        'description' => 'Home Menu Item',
        'icon' => 'fa fa-home',
        'target' => null,
        'order' => 0
    ]);
    Corals\Menu\Models\Menu::updateOrCreate([
        'parent_id' => $footerMenuId,
        'key' => null,
        'url' => 'contact-us',
        'active_menu_url' => 'contact-us',
        'name' => 'Contact Us',
        'description' => 'Contact Us Menu Item',
        'icon' => null,
        'target' => null,
        'order' => 980
    ]);
}


$title = 'Book Our Doctor';

$block = Block::updateOrCreate(['key' => Str::slug($title)], [
    'name' => $title,
    'key' => Str::slug($title),
    'as_row' => false,

]);


Widget::updateOrCreate(['title' => $title], [
    'title' => $title,
    'content' => " <div class=\"section-header \">
                        <h2>Book Our Doctor</h2>
                        <p>Lorem Ipsum is simply dummy text </p>
                    </div>
                    <div class=\"about-content\">
                        <p>It is a long established fact that a reader will be distracted by the readable content of a
                            page when looking at its layout. The point of using Lorem Ipsum.</p>
                        <p>web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem
                            ipsum' will uncover many web sites still in their infancy. Various versions have evolved
                            over the years, sometimes</p>
                        <a href=\"javascript:;\">Read More..</a>
                    </div>
                ",
    'block_id' => $block->id,
    'widget_width' => 4,
    'widget_order' => 0,
    'status' => 'active',
]);
