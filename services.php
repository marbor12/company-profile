<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Services - idSpora</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <div class="main-container">
      <!-- Navigation -->
      <nav class="navbar navbar-expand-lg">
        <div class="container">
          <a class="navbar-brand" href="index.php"> idSpora </a>

          <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
          >
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto me-4">
              <li class="nav-item">
                <a class="nav-link" href="index.php">Home</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="services.php">Services</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="about.php">About Us</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="portfolio.html">Portfolio</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="blog.php">Blog</a>
              </li>
            </ul>
            <a href="contact.php" class="btn btn-contact">Contact Us</a>
          </div>
        </div>
      </nav>

      <!-- Breadcrumb -->
      <nav aria-label="breadcrumb">
        <div class="container">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active">Services</li>
          </ol>
        </div>
      </nav>

      <!-- Page Header -->
      <section class="page-header">
        <div class="container">
          <h1 class="page-title">
            Our <span class="highlight-orange">Professional Services</span>
          </h1>
          <p class="page-subtitle">
            Comprehensive training and event management solutions tailored to
            elevate your organization's success.
          </p>
        </div>
      </section>

      <!-- Services Section -->
      <section class="section-spacing">
        <div class="container">
          <div class="row">
            <!-- Corporate Training -->
            <div class="col-lg-4 col-md-6 mb-4" id="training">
              <div class="service-card">
                <div class="service-icon mb-3">
                  <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h4 class="text-center mb-3">Corporate Training</h4>
                <p class="text-muted mb-4">
                  Comprehensive professional development programs designed to
                  enhance your team's skills and productivity.
                </p>
                <ul class="list-unstyled mb-4">
                  <li>✓ Leadership Development</li>
                  <li>✓ Management Skills</li>
                  <li>✓ Communication Training</li>
                  <li>✓ Digital Skills</li>
                  <li>✓ Soft Skills Development</li>
                </ul>
                <div class="text-center">
                  <div class="h5 text-primary mb-3">
                    Starting from $2,500/program
                  </div>
                  <button class="btn btn-dark">Get Quote</button>
                </div>
              </div>
            </div>

            <!-- Event Management -->
            <div class="col-lg-4 col-md-6 mb-4" id="events">
              <div class="service-card">
                <div class="service-icon mb-3">
                  <i class="fas fa-calendar-alt"></i>
                </div>
                <h4 class="text-center mb-3">Event Management</h4>
                <p class="text-muted mb-4">
                  Full-service event planning and execution for corporate
                  events, conferences, and special occasions.
                </p>
                <ul class="list-unstyled mb-4">
                  <li>✓ Conference Planning</li>
                  <li>✓ Corporate Events</li>
                  <li>✓ Product Launches</li>
                  <li>✓ Award Ceremonies</li>
                  <li>✓ Venue Management</li>
                </ul>
                <div class="text-center">
                  <div class="h5 text-primary mb-3">
                    Starting from $5,000/event
                  </div>
                  <button class="btn btn-dark">Get Quote</button>
                </div>
              </div>
            </div>

            <!-- Team Building -->
            <div class="col-lg-4 col-md-6 mb-4" id="team">
              <div class="service-card">
                <div class="service-icon mb-3">
                  <i class="fas fa-users"></i>
                </div>
                <h4 class="text-center mb-3">Team Building</h4>
                <p class="text-muted mb-4">
                  Engaging team building activities designed to strengthen
                  collaboration and boost team morale.
                </p>
                <ul class="list-unstyled mb-4">
                  <li>✓ Outdoor Activities</li>
                  <li>✓ Problem Solving Games</li>
                  <li>✓ Trust Building Exercises</li>
                  <li>✓ Communication Workshops</li>
                  <li>✓ Leadership Challenges</li>
                </ul>
                <div class="text-center">
                  <div class="h5 text-primary mb-3">
                    Starting from $1,500/session
                  </div>
                  <button class="btn btn-dark">Get Quote</button>
                </div>
              </div>
            </div>

            <!-- Workshops -->
            <div class="col-lg-4 col-md-6 mb-4" id="workshop">
              <div class="service-card">
                <div class="service-icon mb-3">
                  <i class="fas fa-tools"></i>
                </div>
                <h4 class="text-center mb-3">Professional Workshops</h4>
                <p class="text-muted mb-4">
                  Intensive skill-building workshops focused on specific
                  competencies and industry best practices.
                </p>
                <ul class="list-unstyled mb-4">
                  <li>✓ Technical Skills</li>
                  <li>✓ Industry Certifications</li>
                  <li>✓ Innovation Workshops</li>
                  <li>✓ Strategy Sessions</li>
                  <li>✓ Best Practice Sharing</li>
                </ul>
                <div class="text-center">
                  <div class="h5 text-primary mb-3">
                    Starting from $800/workshop
                  </div>
                  <button class="btn btn-dark">Get Quote</button>
                </div>
              </div>
            </div>

            <!-- Consulting -->
            <div class="col-lg-4 col-md-6 mb-4">
              <div class="service-card">
                <div class="service-icon mb-3">
                  <i class="fas fa-lightbulb"></i>
                </div>
                <h4 class="text-center mb-3">Training Consulting</h4>
                <p class="text-muted mb-4">
                  Strategic consulting to identify training needs and develop
                  customized learning solutions.
                </p>
                <ul class="list-unstyled mb-4">
                  <li>✓ Training Needs Analysis</li>
                  <li>✓ Curriculum Development</li>
                  <li>✓ Learning Strategy</li>
                  <li>✓ Performance Assessment</li>
                  <li>✓ ROI Measurement</li>
                </ul>
                <div class="text-center">
                  <div class="h5 text-primary mb-3">
                    Starting from $200/hour
                  </div>
                  <button class="btn btn-dark">Get Quote</button>
                </div>
              </div>
            </div>

            <!-- Online Training -->
            <div class="col-lg-4 col-md-6 mb-4">
              <div class="service-card">
                <div class="service-icon mb-3">
                  <i class="fas fa-laptop"></i>
                </div>
                <h4 class="text-center mb-3">Online Training</h4>
                <p class="text-muted mb-4">
                  Flexible online training programs that can be accessed
                  anywhere, anytime for continuous learning.
                </p>
                <ul class="list-unstyled mb-4">
                  <li>✓ Virtual Classrooms</li>
                  <li>✓ E-Learning Modules</li>
                  <li>✓ Interactive Content</li>
                  <li>✓ Progress Tracking</li>
                  <li>✓ Certification Programs</li>
                </ul>
                <div class="text-center">
                  <div class="h5 text-primary mb-3">
                    Starting from $99/person
                  </div>
                  <button class="btn btn-dark">Get Quote</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Process Section -->
      <section class="section-spacing bg-light">
        <div class="container">
          <h2 class="text-center section-title mb-5">
            Our <span class="highlight-orange">Process</span>
          </h2>
          <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="text-center">
                <div class="service-icon mb-3">
                  <i class="fas fa-search"></i>
                </div>
                <h5>1. Discovery</h5>
                <p class="text-muted">
                  We analyze your needs and objectives to create the perfect
                  solution.
                </p>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="text-center">
                <div class="service-icon mb-3">
                  <i class="fas fa-drafting-compass"></i>
                </div>
                <h5>2. Design</h5>
                <p class="text-muted">
                  Custom program design tailored to your specific requirements.
                </p>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="text-center">
                <div class="service-icon mb-3">
                  <i class="fas fa-play"></i>
                </div>
                <h5>3. Delivery</h5>
                <p class="text-muted">
                  Professional execution with experienced trainers and
                  facilitators.
                </p>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
              <div class="text-center">
                <div class="service-icon mb-3">
                  <i class="fas fa-chart-line"></i>
                </div>
                <h5>4. Evaluation</h5>
                <p class="text-muted">
                  Comprehensive assessment and follow-up for continuous
                  improvement.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- CTA Section -->
      <section class="section-spacing">
        <div class="container text-center">
          <h2 class="section-title mb-4">Ready to Transform Your Team?</h2>
          <p class="lead mb-4">
            Let's discuss how our services can help your organization achieve
            its goals.
          </p>
          <a href="contact.php" class="btn btn-dark btn-lg me-3"
            >Get Started</a
          >
          <button class="btn btn-outline-dark btn-lg">
            Schedule Consultation
          </button>
        </div>
      </section>

      <!-- Footer -->
      <footer class="footer-section">
        <div class="container">
          <div class="row">
            <div class="col-lg-4 mb-4">
              <h5 class="text-white mb-3">
                EDUEVENT PRO
                <div class="logo-shapes">
                  <div class="shape shape-red"></div>
                  <div class="shape shape-blue"></div>
                  <div class="shape shape-green"></div>
                  <div class="shape shape-orange"></div>
                </div>
              </h5>
              <p class="text-light">
                Your trusted partner for professional training and memorable
                events.
              </p>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
              <h6 class="text-white mb-3">Quick Links</h6>
              <ul class="list-unstyled">
                <li><a href="index.php" class="text-light">Home</a></li>
                <li><a href="about.php" class="text-light">About Us</a></li>
                <li><a href="services.php" class="text-light">Services</a></li>
                <li><a href="contact.php" class="text-light">Contact</a></li>
              </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
              <h6 class="text-white mb-3">Services</h6>
              <ul class="list-unstyled">
                <li>
                  <a href="#training" class="text-light">Corporate Training</a>
                </li>
                <li>
                  <a href="#events" class="text-light">Event Management</a>
                </li>
                <li><a href="#team" class="text-light">Team Building</a></li>
                <li><a href="#workshop" class="text-light">Workshops</a></li>
              </ul>
            </div>
            <div class="col-lg-4 mb-4">
              <h6 class="text-white mb-3">Newsletter</h6>
              <p class="text-light">
                Subscribe to get updates on training tips and upcoming events.
              </p>
              <div class="input-group">
                <input
                  type="email"
                  class="form-control"
                  placeholder="Your email"
                />
                <button
                  class="btn"
                  style="background: var(--primary-orange); color: white"
                >
                  Subscribe
                </button>
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
