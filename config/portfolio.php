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
    | through Vite, so leave "image" as null to show a placeholder.
    |
    */

    'categories' => [
        'Drone Fabrication',
        'Simulation & Analysis',
        'Web Development',
    ],

    'projects' => [
        [
            'category' => 'Drone Fabrication',
            'title' => 'F450 Quadcopter',
            'description' => 'Customizable multirotor RPAS platform built for mapping, inspection, and research applications.',
            'image' => 'resources/images/portfolio-f450.jpg',
            'imageFit' => 'object-cover',
            'featured' => true,
        ],
        [
            'category' => 'Drone Fabrication',
            'title' => 'F500 Quadcopter',
            'description' => 'Larger-frame multirotor with GPS and telemetry, suited to surveillance and industrial payloads.',
            'image' => 'resources/images/portfolio-f500.jpg',
            'imageFit' => 'object-cover',
        ],
        [
            'category' => 'Simulation & Analysis',
            'title' => 'Conventional vs Toroidal Propeller CFD',
            'description' => 'SolidWorks CFD comparison at 10,700 RPM. The toroidal design produced 19% more hover thrust, while the conventional 6030 propeller stayed more efficient in forward flight (63.5% vs 40.2%).',
            'image' => 'resources/images/portfolio-propeller-cfd.jpg',
            'imageFit' => 'object-cover',
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
            'image' => 'resources/images/portfolio-air-dryer.jpg',
            'imageFit' => 'object-cover',
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
            'category' => 'Web Development',
            'title' => 'Bagsakan Veggies Online Store',
            'description' => 'Vegetable e-commerce site for Quezon City with bulk 10 kg bags or per-kilo ordering, a live price list, cart, and same-day delivery.',
            'image' => 'resources/images/portfolio-bagsakan-veggies-site.jpg',
            'imageFit' => 'object-contain',
            'url' => 'https://bagsakanveggies.com',
            'featured' => true,
        ],
        [
            'category' => 'Web Development',
            'title' => '3D Printing Services Website',
            'description' => 'Service site with an online filament shop, 3D model gallery, and request-for-quote flow.',
            'image' => 'resources/images/portfolio-3d-printing-site.jpg',
            'imageFit' => 'object-contain',
        ],
        [
            'category' => 'Web Development',
            'title' => 'Camp Ba-Long Resort Website',
            'description' => 'Resort site with online booking, food ordering, and spa service menus.',
            'image' => 'resources/images/portfolio-camp-ba-long-site.jpg',
            'imageFit' => 'object-contain',
        ],
    ],

];
