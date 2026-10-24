<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Portfolio Projects
    |--------------------------------------------------------------------------
    |
    | Every project shown on the site. Projects marked "featured" appear on
    | the homepage; all of them appear on the projects page, grouped by
    | category. Images are paths under resources/images and are resolved
    | through Vite, so leave "image" as null to show a placeholder. An
    | optional "gallery" lists extra images shown when the card is opened.
    |
    */

    'categories' => [
        'Drone Fabrication',
        'Simulation & Analysis',
        'Product Design & Rendering',
        'Web Development',
    ],

    'projects' => [
        [
            'category' => 'Drone Fabrication',
            'title' => 'F450 Quadcopter',
            'description' => 'Customizable multirotor RPAS platform built for mapping, inspection, and research applications.',
            'image' => 'resources/images/drone1.png',
            'imageFit' => 'object-contain',
            'cutout' => true,
            'featured' => true,
        ],
        [
            'category' => 'Drone Fabrication',
            'title' => 'F500 Quadcopter',
            'description' => 'Larger-frame multirotor with GPS and telemetry, suited to surveillance and industrial payloads.',
            'image' => 'resources/images/drone2.png',
            'imageFit' => 'object-contain',
            'cutout' => true,
        ],
        [
            'category' => 'Drone Fabrication',
            'title' => 'Camera Drone CAD Design',
            'description' => 'Complete 3D CAD model of a carbon-frame X-quadcopter with a gimbal-mounted camera, including an exploded view of the frame, arms, motors, and propellers.',
            'image' => 'resources/images/projects/quadcopter-design-cutout.png',
            'imageFit' => 'object-contain',
            'cutout' => true,
            'gallery' => [
                'resources/images/projects/quadcopter-design-cutout.png',
                'resources/images/projects/quadcopter-design-2-cutout.png',
                'resources/images/projects/quadcopter-design-3-cutout.png',
                'resources/images/projects/quadcopter-design-4-cutout.png',
                'resources/images/projects/quadcopter-design-5-cutout.png',
                'resources/images/projects/quadcopter-design-6-cutout.png',
            ],
        ],
        [
            'category' => 'Simulation & Analysis',
            'title' => 'Conventional vs Toroidal Propeller CFD',
            'description' => 'SolidWorks CFD comparison at 10,700 RPM. The toroidal design produced 19% more hover thrust, while the conventional 6030 propeller stayed more efficient in forward flight (63.5% vs 40.2%).',
            'image' => 'resources/images/portfolio-propeller-cfd.jpg',
            'imageFit' => 'object-contain',
            'cutout' => true,
            'panelMockup' => true,
            'panelAspectRatio' => '4 / 3',
            'featured' => true,
        ],
        [
            'category' => 'Simulation & Analysis',
            'title' => 'Wing Structural Analysis',
            'description' => 'Static FEA of a PETG, carbon fiber, and plywood wing prototype with a mesh independence study. Peak stress stayed at 1.55 MPa, far below the 40 MPa yield strength.',
            'image' => 'resources/images/portfolio-wing-fea.jpg',
            'imageFit' => 'object-cover',
        ],
        [
            'category' => 'Simulation & Analysis',
            'title' => 'Compressed Air Piping Network CFD',
            'description' => 'Internal flow study of an 8-zone diffuser network that traced pressure losses and dead zones back to elbows and long runs, then validated a dual-source layout that restored flow downstream.',
            'image' => 'resources/images/portfolio-piping-cfd.jpg',
            'imageFit' => 'object-cover',
        ],
        [
            'category' => 'Simulation & Analysis',
            'title' => 'Air-Dryer Chamber Thermal Analysis',
            'description' => 'CFD and thermal optimization of a 3-tray induction-heated dryer, tuned to hold 40–55 °C at 5 CFM and 500 W, delivered with a sensor and controller BOM and system block diagram.',
            'image' => 'resources/images/projects/air-dryer-1-cutout.png',
            'imageFit' => 'object-contain',
            'cutout' => true,
            'gallery' => [
                'resources/images/projects/air-dryer-1-cutout.png',
                'resources/images/projects/air-dryer-2-cutout.png',
                'resources/images/projects/air-dryer-3-cutout.png',
                'resources/images/projects/air-dryer-4-cutout.png',
                'resources/images/projects/air-dryer-5-cutout.png',
                'resources/images/projects/air-dryer-6-cutout.png',
            ],
        ],
        [
            'category' => 'Simulation & Analysis',
            'title' => 'Heat Exchanger Tube Geometry Study',
            'description' => 'Ansys Fluent thermal analysis comparing circular and hexagonal tubes at four orientations for heat transfer, temperature, and pressure performance.',
            'image' => 'resources/images/portfolio-heat-exchanger.jpg',
            'imageFit' => 'object-cover',
        ],
        [
            'category' => 'Simulation & Analysis',
            'title' => 'Vehicle External Aerodynamics',
            'description' => 'Ansys Fluent study of a vehicle body at 0–15° flow angles and 60–100 kph, mapping pressure, velocity streamlines, turbulent kinetic energy, and wake profile for each case.',
            'image' => 'resources/images/portfolio-vehicle-aero.jpg',
            'imageFit' => 'object-cover',
        ],
        [
            'category' => 'Product Design & Rendering',
            'title' => 'Service Robot Design',
            'description' => 'Product design of an indoor service robot with a touchscreen, sensor head, drawer compartments, and a LiDAR-equipped wheeled base, rendered with exploded views of its internal components.',
            'image' => 'resources/images/projects/service-robot-1-cutout.png',
            'imageFit' => 'object-contain',
            'cutout' => true,
            'gallery' => [
                'resources/images/projects/service-robot-1-cutout.png',
                'resources/images/projects/service-robot-2-cutout.png',
                'resources/images/projects/service-robot-3-cutout.png',
                'resources/images/projects/service-robot-4-cutout.png',
                'resources/images/projects/service-robot-5-cutout.png',
                'resources/images/projects/service-robot-6-cutout.png',
                'resources/images/projects/service-robot-7-cutout.png',
                'resources/images/projects/service-robot-8-cutout.png',
            ],
        ],
        [
            'category' => 'Web Development',
            'title' => 'Bagsakan Veggies Online Store',
            'description' => 'Vegetable e-commerce site for Quezon City with bulk 10 kg bags or per-kilo ordering, a live price list, cart, and same-day delivery.',
            'image' => 'resources/images/bagsakan.png',
            'imageFit' => 'object-contain',
            'cutout' => true,
            'url' => 'https://bagsakanveggies.com',
            'featured' => true,
        ],
        [
            'category' => 'Web Development',
            'title' => '3D Printing Services Website',
            'description' => 'Service site with an online filament shop, 3D model gallery, and request-for-quote flow.',
            'image' => 'resources/images/portfolio-3d-printing-site.jpg',
            'imageFit' => 'object-contain',
            'cutout' => true,
            'panelMockup' => true,
        ],
        [
            'category' => 'Web Development',
            'title' => 'Camp Ba-Long Resort Website',
            'description' => 'Resort site with online booking, food ordering, and spa service menus.',
            'image' => 'resources/images/portfolio-camp-ba-long-site.jpg',
            'imageFit' => 'object-contain',
            'cutout' => true,
            'panelMockup' => true,
        ],
    ],

];
