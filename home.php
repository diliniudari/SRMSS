<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SRMSS - Smart Route Management and Scheduling System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        /* NAVBAR */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 60px;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-text h2 {
            font-size: 20px;
            color: #1a73e8;
            font-weight: bold;
        }

        .logo-text p {
            font-size: 10px;
            color: #888;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-size: 14px;
            font-weight: 500;
        }

        .nav-links a:hover { color: #1a73e8; }

        .nav-links a.active {
            color: #1a73e8;
            border-bottom: 2px solid #1a73e8;
            padding-bottom: 3px;
        }

        .nav-btn {
            background: #1a73e8;
            color: white !important;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: bold !important;
        }

        .nav-btn:hover {
            background: #1558b0 !important;
        }

        /* HERO SECTION */
        .hero {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            padding: 60px;
            background: linear-gradient(135deg, #f0f7ff 0%, #e8f0fe 100%);
            min-height: 90vh;
            position: relative;
            overflow: hidden;
            gap: 40px;
        }

        /* LEFT SIDE */
        .hero-text {
            position: relative;
            z-index: 2;
        }

        .hero-text h1 {
            font-size: 42px;
            font-weight: bold;
            color: #1a1a2e;
            line-height: 1.2;
            margin-bottom: 15px;
        }

        .hero-text h1 span { color: #1a73e8; }

        .hero-text p {
            font-size: 16px;
            color: #555;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .hero-btn {
            display: inline-block;
            background: #1a73e8;
            color: white;
            padding: 15px 35px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .hero-btn:hover {
            background: #1558b0;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(26,115,232,0.4);
        }

        /* RIGHT SIDE */
        .hero-right {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* DASHBOARD MOCKUP */
        .hero-image {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            width: 100%;
            border: 1px solid #e0e0e0;
        }

        .mockup-header {
            background: #1a73e8;
            border-radius: 8px;
            padding: 10px 15px;
            color: white;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .mockup-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .mockup-stat {
            background: #f8f9ff;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            border: 1px solid #e8f0fe;
        }

        .mockup-stat .num {
            font-size: 20px;
            font-weight: bold;
            color: #1a73e8;
        }

        .mockup-stat .label {
            font-size: 10px;
            color: #888;
            margin-top: 3px;
        }

        .mockup-row { display: flex; gap: 10px; }

        .mockup-box {
            background: #f8f9ff;
            border-radius: 8px;
            padding: 12px;
            flex: 1;
            border: 1px solid #e8f0fe;
        }

        .mockup-box h4 {
            font-size: 11px;
            color: #333;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .mockup-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
            font-size: 10px;
        }

        .mockup-badge {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        .mockup-badge.delayed {
            background: #fff3e0;
            color: #e65100;
        }

        /* BUS IMAGE */
        .bus-image {
            width: 100%;
            height: 270px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* FEATURES SECTION */
        .features {
            padding: 60px;
            background: white;
        }

        .features h2 {
            text-align: center;
            font-size: 32px;
            color: #1a1a2e;
            margin-bottom: 10px;
        }

        .features p.sub {
            text-align: center;
            color: #888;
            margin-bottom: 40px;
            font-size: 15px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
        }

        .feature-card {
            background: #f8f9ff;
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            border: 1px solid #e8f0fe;
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(26,115,232,0.15);
        }

        .feature-card .icon {
    font-size: 35px;
    margin-bottom: 12px;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px auto;
}

.feature-card .icon img {
    width: 70px;
    height: 70px;
    object-fit: contain;
}

        .feature-card h3 {
            font-size: 14px;
            color: #1a1a2e;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .feature-card p {
            font-size: 12px;
            color: #888;
            line-height: 1.5;
        }

        /* STATS SECTION */
        .stats-section {
            background: #1a73e8;
            padding: 60px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            text-align: center;
        }

        .stat-item h2 {
            font-size: 42px;
            color: white;
            font-weight: bold;
        }

        .stat-item p {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            margin-top: 5px;
        }

        /* FOOTER */
        .footer {
            background: #1a1a2e;
            padding: 25px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .footer-left p {
            color: rgba(255,255,255,0.7);
            font-size: 13px;
        }

        .footer-right {
            display: flex;
            gap: 25px;
        }

        .footer-right span {
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <!-- LOGO LEFT -->
    <div class="logo">
        <img src="logo.png" 
             alt="SRMSS Logo"
             style="width: 80px; height: 55px; object-fit: contain;">
        <div class="logo-text">
            <h2>SRMSS</h2>
            <p>Smart Route Management and Scheduling System</p>
        </div>
    </div>

    <!-- NAV LINKS RIGHT -->
    <ul class="nav-links">
        <li><a href="home.php" class="active">Home</a></li>
        <li><a href="#features">Features</a></li>
        <li><a href="#benefits">Benefits</a></li>
        <li><a href="#stats">Solutions</a></li>
        <li><a href="index.php" class="nav-btn">Login →</a></li>
    </ul>
</nav>

<!-- HERO SECTION -->
<section class="hero">

    <!-- LEFT SIDE - IMAGE + TEXT -->
    <div class="hero-text">
        
        <!-- CITY IMAGE -->
        <img src="uuu.jpg" 
             alt="SRMSS Image"
             style="
                width: 100%;
                height: 248px;
                object-fit: cover;
                border-radius: 12px;
                margin-bottom: 20px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.15);
             ">

        <h1>Smart Route Management and <span>Scheduling System</span></h1>
        <p style="color:#1a73e8; font-weight:bold; font-size:16px; margin-bottom:10px;">
            (SRMSS) for Public Transport Depots
        </p>
        <p>Intelligent route planning. Optimized scheduling. 
           Efficient operations. Better public transport for all.</p>
        <a href="index.php" class="hero-btn">Explore Solutions →</a>
    </div>

    <!-- RIGHT SIDE - DASHBOARD + BUS -->
    <div class="hero-right">

        <!-- DASHBOARD MOCKUP -->
        <div class="hero-image">
            <div class="mockup-header"> SRMSS Dashboard</div>
            
            <div class="mockup-stats">
                <div class="mockup-stat">
                    <div class="num">5</div>
                    <div class="label">Total Buses</div>
                </div>
                <div class="mockup-stat">
                    <div class="num">5</div>
                    <div class="label">Active Routes</div>
                </div>
                <div class="mockup-stat">
                    <div class="num">5</div>
                    <div class="label">Today's Trips</div>
                </div>
                <div class="mockup-stat">
                    <div class="num">92%</div>
                    <div class="label">On-Time</div>
                </div>
            </div>

            <div class="mockup-row">
                <div class="mockup-box">
                    <h4>📅 Schedule Overview</h4>
                    <div class="mockup-item">
                        <span>07:00 AM - Colombo→Kandy</span>
                        <span class="mockup-badge">On Time</span>
                    </div>
                    <div class="mockup-item">
                        <span>08:00 AM - Colombo→Galle</span>
                        <span class="mockup-badge delayed">Delayed</span>
                    </div>
                    <div class="mockup-item">
                        <span>09:00 AM - Kandy→Jaffna</span>
                        <span class="mockup-badge">On Time</span>
                    </div>
                    <div class="mockup-item">
                        <span>10:00 AM - Colombo→Matara</span>
                        <span class="mockup-badge">On Time</span>
                    </div>
                </div>
                <div class="mockup-box">
                    <h4>🏢 Depot Status</h4>
                    <div class="mockup-item">
                        <span>Colombo Depot</span>
                        <span class="mockup-badge">Active 3</span>
                    </div>
                    <div class="mockup-item">
                        <span>Kandy Depot</span>
                        <span class="mockup-badge">Active 1</span>
                    </div>
                    
                </div>
            </div>
        </div>

        <!-- BUS IMAGE -->
        <img src="red-bus-sri-lanka.jpg" 
             alt="Sri Lanka Bus"
             class="bus-image">
    </div>

</section>

<!-- FEATURES SECTION -->
<section class="features" id="features">
    <h2>Key Features</h2>
    <p class="sub">Everything you need to manage your depot efficiently</p>

    <div class="features-grid">
        <div class="feature-card">
            <div class="icon">
                <img src="smart.png" alt="Smart Route Planning"
                     style="width:60px; height:70px; object-fit:contain;">
            </div>
            <h3>Smart Route Planning</h3>
            <p>Create and manage routes with stops and distance tracking</p>
        </div>
        <div class="feature-card">
            <div class="icon">
                <img src="inteli.png" alt="Intelligent Scheduling"
                     style="width:60px; height:95px; object-fit:contain;">
            </div>
            <h3>Intelligent Scheduling</h3>
            <p>Optimized schedules to reduce delays and downtime</p>
        </div>
		
        <div class="feature-card">
            <div class="icon">
			<img src="reelable.png" alt="Real-Time Monitoring"
			    style="width:60px; height:170px; object-fit:contain;">
	   </div>
            <h3>Real-Time Monitoring</h3>
            <p>Live trip status and alerts for better operational control</p>
        </div>
		
        <div class="feature-card">
            <div class="icon">
                <img src="datad.webp" alt="Data Driven Insights"
                     style="width:60px; height:50px; object-fit:contain;">			 
            </div>
            <h3>Data-Driven Insights</h3>
            <p>Actionable analytics for smarter decision making</p>
        </div>
        <div class="feature-card">
            <div class="icon">
                <img src="relia.png" alt="Reliable and Scalable"
                     style="width:80px; height:50px; object-fit:contain;">
            </div>
            <h3>Reliable & Scalable</h3>
            <p>Secure, scalable and built for modern transport needs</p>
        </div>
    </div>
</section>

<!-- STATS SECTION -->
<section class="stats-section" id="stats">
    <div class="stat-item">
        <h2></h2>
        <h2>Improve</h2>
        <p>Efficiency</p>
    </div>
    <div class="stat-item">
        <h2></h2>
        <h2>Reduce</h2>
        <p>Costs</p>
    </div>
    <div class="stat-item">
        <h2></h2>
        <h2>Enhance</h2>
        <p>Passenger Experience</p>
    </div>
    <div class="stat-item">
        <h2></h2>
        <h2>Sustainable</h2>
        <p>Future</p>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-left">
        <img src="logo.png" style="width:80px; height:40px; object-fit:contain;">
        <p>Empowering Public Transport Depots with Technology for Smarter Cities</p>
    </div>
    <div class="footer-right">
        <span>🚀 Improve Efficiency</span>
        <span>💰 Reduce Costs</span>
        <span>😊 Enhance Passenger Experience</span>
        <span>🌱 Sustainable Future</span>
    </div>
</footer>

</body>
</html>