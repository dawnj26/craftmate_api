<!DOCTYPE html>
<html>
  <head>
    <title>CraftMate</title>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, user-scalable=no"
    />
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome-all.min.css') }}" />
    <noscript
      ><link rel="stylesheet" href="{{ asset('assets/css/noscript.css') }}"
    /></noscript>
  </head>
  <body class="is-preload">
    <!-- Wrapper -->
    <div id="wrapper">
      <!-- Header -->
      <header id="header" class="alt">
        <span class="logo"
          ><img id="logo-craftmate" src="{{ asset('assets/images/logo_without_label.png') }}" alt=""
        /></span>
        <h1 id="app-title">CraftMate</h1>
      </header>

      <!-- Nav -->
      <nav id="nav">
        <ul>
          <li><a href="#intro" class="active">Introduction</a></li>
          <li><a href="#first">Features</a></li>
        </ul>
      </nav>

      <!-- Main -->
      <div id="main">
        <!-- Introduction -->
        <section id="intro" class="main">
          <div class="spotlight">
            <div class="content">
              <header class="major">
                <h2>Unleash Your Creativity</h2>
              </header>
              <p>
                Discover the world of crafting possibilities! CraftMate is your
                ultimate crafting companion.
              </p>
              <ul class="actions">
                <li>
                  <a href="{{ $downloadUrl }}" class="button primary">Downlad now!</a>
                </li>
              </ul>
            </div>
            <span class="image"><img src="{{ asset('assets/images/page_1.png') }}" alt="" /></span>
          </div>
        </section>

        <!-- First Section -->
        <section id="first" class="main special">
          <header class="major">
            <h2>Features</h2>
          </header>
          <ul class="features">
            <li>
              <span class="icon solid major style1 fa-code"></span>
              <h3>AI Recommendations</h3>
              <p>
                Let CraftMate’s AI suggest unique projects tailored to what you
                have.
              </p>
            </li>
            <li>
              <span class="icon major style3 fa-copy"></span>
              <h3>Learn & Create</h3>
              <p>
                Access detailed instructions, videos, and step-by-step guidance
                for each project.
              </p>
            </li>
            <li>
              <span class="icon major style5 fa-gem"></span>
              <h3>Connect with Crafters</h3>
              <p>
                Share your creations, provide feedback, and get inspired by a
                vibrant crafting community.
              </p>
            </li>
          </ul>
        </section>
      </div>

      <!-- Footer -->
      <footer id="footer">
        <section>
          <h2>About Our Services</h2>
          <p>
            We specialize in delivering innovative solutions tailored to your
            needs. Our team of experts is committed to providing exceptional
            service and support to help your business grow and succeed in
            today's competitive market.
          </p>
          <ul class="actions">
            <li>
              <a href="#" class="button footer-button">Learn More</a>
            </li>
          </ul>
        </section>
        <section>
          <h2>Contact Us</h2>
          <dl class="alt">
            <dt>Address</dt>
            <dd>
              PSU Urdaneta City Campus &bull; Urdaneta, Pangasinan &bull;
              Philippines
            </dd>
            <dt>Phone</dt>
            <dd>(+63) 9560575513</dd>
            <dt>Email</dt>
            <dd>
              <a href="mailto:contact@company.com">contact@craftmate.app</a>
            </dd>
          </dl>
          <ul class="icons">
            <li>
              <a href="#" class="icon brands fa-twitter alt"
                ><span class="label">Twitter</span></a
              >
            </li>
            <li>
              <a href="#" class="icon brands fa-facebook-f alt"
                ><span class="label">Facebook</span></a
              >
            </li>
            <li>
              <a href="#" class="icon brands fa-instagram alt"
                ><span class="label">Instagram</span></a
              >
            </li>
            <li>
              <a href="#" class="icon brands fa-github alt"
                ><span class="label">GitHub</span></a
              >
            </li>
            <li>
              <a href="#" class="icon brands fa-dribbble alt"
                ><span class="label">Dribbble</span></a
              >
            </li>
          </ul>
        </section>
      </footer>
    </div>

    <!-- Scripts -->
    <script src="https://kit.fontawesome.com/331539da09.js" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.scrollex.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.scrolly.min.js') }}"></script>
    <script src="{{ asset('assets/js/browser.min.js') }}"></script>
    <script src="{{ asset('assets/js/breakpoints.min.js') }}"></script>
    <script src="{{ asset('assets/js/util.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
  </body>
</html>
