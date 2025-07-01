<<?php
require 'INCLUDES/db_connects.php';

// Get team members info
$team_members = [
    [
        'name' => 'David Njoroge',
        'position' => 'Chief Executive Officer',
        'image' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=400&q=80',
        'linkedin' => '#',
        'twitter' => '#'
    ],
    [
        'name' => 'Sarah Omondi',
        'position' => 'Chief Operations Officer',
        'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=400&q=80',
        'linkedin' => '#',
        'twitter' => '#'
    ],
    [
        'name' => 'Michael Kamau',
        'position' => 'Chief Technology Officer',
        'image' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=400&q=80',
        'linkedin' => '#',
        'twitter' => '#'
    ],
    [
        'name' => 'Grace Mwangi',
        'position' => 'Chief Financial Officer',
        'image' => 'https://images.unsplash.com/photo-1551836022-d5d88e9218df?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=400&q=80',
        'linkedin' => '#',
        'twitter' => '#'
    ]
];

// Get timeline history
$timeline_events = [
    [
        'year' => '2010',
        'description' => 'Apex Assurance was founded in Nairobi with a vision to transform the insurance industry in Kenya.'
    ],
    [
        'year' => '2013',
        'description' => 'Expanded operations to Mombasa and Kisumu, reaching more Kenyans with our insurance solutions.'
    ],
    [
        'year' => '2015',
        'description' => 'Launched our first digital platform, allowing customers to access basic insurance services online.'
    ],
    [
        'year' => '2018',
        'description' => 'Received the "Innovation in Insurance" award from the Insurance Regulatory Authority of Kenya.'
    ],
    [
        'year' => '2020',
        'description' => 'Celebrated our 10th anniversary with the launch of our comprehensive digital transformation strategy.'
    ],
    [
        'year' => '2025',
        'description' => 'Launched our cutting-edge Motor Vehicle Accident Management System, revolutionizing the claims process in Kenya.'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Apex Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        :root {
            --primary-color: #0056b3;
            --secondary-color: #00b359;
            --dark-color: #333;
            --light-color: #f4f4f4;
            --danger-color: #dc3545;
            --success-color: #28a745;
        }
        
        body {
            line-height: 1.6;
            background-color: var(--light-color);
            color: var(--dark-color);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Styles */
        header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 100;
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        .logo a {
            text-decoration: none;
            color: var(--dark-color);
        }
        
        .nav-links {
            display: flex;
            list-style: none;
        }
        
        .nav-links li {
            margin-left: 25px;
        }
        
        .nav-links a {
            text-decoration: none;
            color: var(--dark-color);
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: var(--primary-color);
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0045a2;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
            margin-left: 10px;
        }
        
        .btn-secondary:hover {
            background-color: #009e4c;
            transform: translateY(-2px);
        }
        
        /* Main Content */
        main {
            margin-top: 80px;
            min-height: calc(100vh - 80px - 60px); /* viewport height minus header and footer */
        }
        
        /* Hero Section */
        .about-hero {
            background: linear-gradient(to right, rgba(0, 86, 179, 0.9), rgba(0, 179, 89, 0.9)), 
                        url('https://images.unsplash.com/photo-1526289034009-0240ddb68ce3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1200&q=80') no-repeat center center/cover;
            color: white;
            text-align: center;
            padding: 100px 0;
        }
        
        .about-hero h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .about-hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
        }
        
        /* Company Info Section */
        .company-info {
            padding: 80px 0;
            background-color: white;
        }
        
        .company-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }
        
        .company-content h2 {
            font-size: 2rem;
            margin-bottom: 20px;
            color: var(--primary-color);
        }
        
        .company-content p {
            margin-bottom: 15px;
        }
        
        .company-image img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Mission/Vision Section */
        .mission-vision {
            padding: 80px 0;
            background-color: var(--light-color);
        }
        
        .mission-vision-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }
        
        .mission-card, .vision-card {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        
        .card-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        
        .mission-card h3, .vision-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: var(--primary-color);
        }
        
        /* Team Section */
        .our-team {
            padding: 80px 0;
            background-color: white;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-title h2 {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .section-title p {
            max-width: 700px;
            margin: 0 auto;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .team-member {
            background-color: var(--light-color);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }
        
        .team-member:hover {
            transform: translateY(-10px);
        }
        
        .member-image {
            height: 250px;
            overflow: hidden;
        }
        
        .member-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        
        .team-member:hover .member-image img {
            transform: scale(1.05);
        }
        
        .member-info {
            padding: 20px;
            text-align: center;
        }
        
        .member-info h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }
        
        .member-info p {
            color: #777;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        
        .social-icons a {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s;
        }
        
        .social-icons a:hover {
            background-color: var(--secondary-color);
        }
        
        /* Timeline Section */
        .our-history {
            padding: 80px 0;
            background-color: var(--light-color);
        }
        
        .timeline {
            position: relative;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px 0;
        }
        
        .timeline::after {
            content: '';
            position: absolute;
            width: 4px;
            background-color: var(--primary-color);
            top: 0;
            bottom: 0;
            left: 50%;
            margin-left: -2px;
        }
        
        .timeline-item {
            position: relative;
            width: 50%;
            padding: 20px 40px;
            box-sizing: border-box;
        }
        
        .timeline-item:nth-child(odd) {
            left: 0;
        }
        
        .timeline-item:nth-child(even) {
            left: 50%;
        }
        
        .timeline-content {
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            position: relative;
        }
        
        .timeline-content h3 {
            font-size: 1.3rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .timeline-item:nth-child(odd) .timeline-content::after {
            content: '';
            position: absolute;
            width: 25px;
            height: 25px;
            right: -67px;
            top: 15px;
            border-radius: 50%;
            background-color: white;
            border: 4px solid var(--primary-color);
            z-index: 1;
        }
        
        .timeline-item:nth-child(even) .timeline-content::after {
            content: '';
            position: absolute;
            width: 25px;
            height: 25px;
            left: -67px;
            top: 15px;
            border-radius: 50%;
            background-color: white;
            border: 4px solid var(--primary-color);
            z-index: 1;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 80px 0;
            background: linear-gradient(to right, #0056b3, #00b359);
            color: white;
            text-align: center;
        }
        
        .cta-section h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }
        
        .cta-section p {
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto 30px;
        }
        
        /* Footer */
        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 20px 0;
        }
        
        .footer-bottom {
            text-align: center;
        }
        
        /* Responsive */
        @media (max-width: 991px) {
            .company-grid,
            .mission-vision-grid {
                grid-template-columns: 1fr;
            }
            
            .timeline::after {
                left: 31px;
            }
            
            .timeline-item {
                width: 100%;
                padding-left: 70px;
                padding-right: 25px;
            }
            
            .timeline-item:nth-child(odd),
            .timeline-item:nth-child(even) {
                left: 0;
            }
            
            .timeline-item:nth-child(odd) .timeline-content::after,
            .timeline-item:nth-child(even) .timeline-content::after {
                left: -36px;
                right: auto;
            }
        }
        
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .about-hero h1 {
                font-size: 2rem;
            }
            
            .about-hero p,
            .company-content h2,
            .section-title h2,
            .cta-section h2 {
                font-size: 1.5rem;
            }
            
            .about-hero {
                padding: 80px 0;
            }
            
            .company-info,
            .mission-vision,
            .our-team,
            .our-history,
            .cta-section {
                padding: 60px 0;
            }
        }
    </style>
</head>
<body>
    <!-- Header with Navigation -->
    <header>
        <div class="container">
            <nav>
                <div class="logo">
                    <a href="Index.php">
                        <h2>APEX ASSURANCE</h2>
                    </a>
                </div>
                <ul class="nav-links">
                    <li><a href="Index.php">Home</a></li>
                    <li><a href="about_us.php">About Us</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
                <div>
                    <a href="login.php" class="btn btn-primary">Login</a>
                    <a href="signup.php" class="btn btn-secondary">Register</a>
                </div>
            </nav>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="about-hero">
            <div class="container">
                <h1>About Apex Assurance</h1>
                <p>Leading the way in innovative insurance solutions for Kenyans since 2010. We combine technology with personalized service to protect what matters most to you.</p>
            </div>
        </section>

        <!-- Company Info Section -->
        <section class="company-info">
            <div class="container">
                <div class="company-grid">
                    <div class="company-content">
                        <h2>Who We Are</h2>
                        <p>Apex Assurance is a leading insurance provider in Kenya, offering comprehensive motor vehicle insurance solutions tailored to meet the diverse needs of our clients. With over a decade of experience in the industry, we have established ourselves as a trusted partner for thousands of vehicle owners across the country.</p>
                        <p>We pride ourselves on our innovative approach to insurance, leveraging technology to streamline the claims process and provide quick, efficient service when our clients need it most. Our Motor Vehicle Accident Management System represents our commitment to digital transformation in the insurance sector.</p>
                        <p>At Apex Assurance, we believe that insurance should be accessible, understandable, and reliable. We work tirelessly to ensure that our policies are transparent, our premiums are competitive, and our service is exceptional.</p>
                    </div>
                    <div class="company-image">
                        <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=600&q=80" alt="Apex Assurance Office">
                    </div>
                </div>
            </div>
        </section>

        <!-- Mission/Vision Section -->
        <section class="mission-vision">
            <div class="container">
                <div class="mission-vision-grid">
                    <div class="mission-card">
                        <div class="card-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h3>Our Mission</h3>
                        <p>To provide innovative, affordable, and reliable insurance solutions that protect our clients and give them peace of mind, while continuously improving our services through technology and customer-centric approaches.</p>
                    </div>
                    <div class="vision-card">
                        <div class="card-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h3>Our Vision</h3>
                        <p>To be the leading digital insurance provider in East Africa, recognized for our excellence in customer service, technological innovation, and commitment to social responsibility.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section class="our-team">
            <div class="container">
                <div class="section-title">
                    <h2>Our Leadership Team</h2>
                    <p>Meet the experienced professionals who guide our company towards excellence and innovation in the insurance industry.</p>
                </div>
                <div class="team-grid">
                    <?php foreach ($team_members as $member): ?>
                    <div class="team-member">
                        <div class="member-image">
                            <img src="<?php echo htmlspecialchars($member['image']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                        </div>
                        <div class="member-info">
                            <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                            <p><?php echo htmlspecialchars($member['position']); ?></p>
                            <div class="social-icons">
                                <?php if (!empty($member['linkedin'])): ?>
                                <a href="<?php echo htmlspecialchars($member['linkedin']); ?>"><i class="fab fa-linkedin-in"></i></a>
                                <?php endif; ?>
                                <?php if (!empty($member['twitter'])): ?>
                                <a href="<?php echo htmlspecialchars($member['twitter']); ?>"><i class="fab fa-twitter"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Timeline Section -->
        <section class="our-history">
            <div class="container">
                <div class="section-title">
                    <h2>Our Journey</h2>
                    <p>From a small startup to one of Kenya's leading insurance providers</p>
                </div>
                <div class="timeline">
                    <?php foreach ($timeline_events as $index => $event): ?>
                    <div class="timeline-item">
                        <div class="timeline-content">
                            <h3><?php echo htmlspecialchars($event['year']); ?></h3>
                            <p><?php echo htmlspecialchars($event['description']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <h2>Join the Apex Assurance Family</h2>
                <p>Experience the difference with our innovative insurance solutions and exceptional customer service.</p>
                <a href="register.php" class="btn btn-secondary">Get Started Today</a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2025 Apex Assurance. All Rights Reserved. Developed by Eunice Kamau BBIT/2022/49483</p>
            </div>
        </div>
    </footer>
</body>
</html>
