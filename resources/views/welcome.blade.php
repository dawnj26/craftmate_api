<!DOCTYPE html>
<html>

<head>
    <title>CraftMate</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome-all.min.css') }}" />
    <noscript>
        <link rel="stylesheet" href="{{ asset('assets/css/noscript.css') }}" />
    </noscript>
    <style>
        .testimonials {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .testimonial {
            padding: 1rem;
            border-radius: 5px;

            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 100%;
            flex: 1 1 300px;
            /* Base width of 300px, can grow and shrink */
            max-width: 35rem;
            /* Prevent too wide testimonials */
        }

        .testimonial .testimonial-content {
            background-color: #f9f9f9;
            padding: 1rem;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);

            flex: 1;
        }

        .testimonial .testimonial-author {
            display: flex;
            align-items: center;
            margin-top: 1rem;
        }

        .testimonial .testimonial-author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 1rem;
        }

        .testimonial .testimonial-author .author-info {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .testimonial .testimonial-author .author-info h4,
        .testimonial .testimonial-author .author-info p,
        .testimonial .testimonial-content p {
            margin: 0;
        }

        @media screen and (max-width: 768px) {
            .testimonial {
                flex: 1 1 100%;
                /* Full width on smaller screens */
            }
        }

        /* Add container for testimonials */
        .testimonials-container {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: center;
        }

        .testimonial-content {
            flex-grow: 1;
            /* Allow content to fill available space */
        }

        /* Accordion Styling */
        .accordion {
            text-align: left;
            max-width: 800px;
            margin: 0 auto;
        }

        .accordion details {
            margin-bottom: 1rem;
            background: #f9f9f9;
            padding: 1rem;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .accordion summary {
            cursor: pointer;
            font-weight: bold;
            margin: -1rem;
            padding: 1rem;
        }

        .accordion details p {
            margin-top: 1rem;
            padding: 0 1rem;
        }
    </style>
</head>

<body class="is-preload">
    <!-- Wrapper -->
    <div id="wrapper">
        <!-- Header -->
        <header id="header" class="alt">
            <span class="logo"><img id="logo-craftmate" src="{{ asset('assets/images/logo_without_label.png') }}"
                    alt="" /></span>
            <h1 id="app-title">CraftMate</h1>
        </header>

        <!-- Nav -->
        <nav id="nav">
            <ul>
                <li><a href="#intro" class="active">Introduction</a></li>
                <li><a href="#first">Features</a></li>
                <li><a href="#second">Testimonials</a></li>
                <li><a href="#third">FAQs</a></li>
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
            <section id="second" class="main special">
                <header class="major">

                    <h2>Testimonials</h2>
                </header>
                <div class="testimonials-container">
                    <div class="testimonial">
                        <div class="testimonial-content">
                            <p>"CraftMate transformed my crafting journey! The AI recommendations are spot-on, and I've
                                created things I never thought I could. It's like having a personal crafting mentor in
                                my
                                pocket."</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="https://picsum.photos/200" alt="" />
                            <div class="author-info">
                                <h4>Sarah Johnson</h4>
                                <p>Hobby Crafter</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial">
                        <div class="testimonial-content">
                            <p>"As a professional artist, I was skeptical at first, but CraftMate's project suggestions
                                and
                                community feedback have helped me explore new techniques and materials I wouldn't have
                                considered otherwise."</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="https://picsum.photos/201" alt="" />
                            <div class="author-info">
                                <h4>Michael Chen</h4>
                                <p>Professional Artist</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial">
                        <div class="testimonial-content">
                            <p>"The step-by-step guides are incredibly detailed. I've gone from being a complete
                                beginner to
                                confidently tackling complex projects. The video tutorials are especially helpful!"</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="https://picsum.photos/202" alt="" />
                            <div class="author-info">
                                <h4>Emily Rodriguez</h4>
                                <p>DIY Enthusiast</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial">
                        <div class="testimonial-content">
                            <p>"What I love most about CraftMate is the supportive community. Everyone shares tips and
                                encouragement, making the crafting journey so much more enjoyable and inspiring."</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="https://picsum.photos/203" alt="" />
                            <div class="author-info">
                                <h4>Thomas Wilson</h4>
                                <p>Crafting Blogger</p>
                            </div>
                        </div>
                    </div>
                    <div class="testimonial">
                        <div class="testimonial-content">
                            <p>"CraftMate's resource management feature has helped me organize my supplies and find
                                creative
                                ways to use materials I already have. It's both practical and inspiring!"</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="https://picsum.photos/204" alt="" />
                            <div class="author-info">
                                <h4>Lisa Parker</h4>
                                <p>Sustainable Crafter</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="third" class="main special">
                <header class="major">

                    <h2>FAQs</h2>
                </header>

                <div class="accordion">
                    <details>
                        <summary>What is CraftMate?</summary>
                        <p>CraftMate is a mobile application designed to inspire creativity and assist artisans in finding personalized craft projects based on the materials they have available. The app recommends crafting ideas, offers tutorials, allows users to manage their material inventory, and promotes community interaction within the crafting space.</p>
                    </details>
                    <details>
                        <summary>How does CraftMate recommend crafting projects?</summary>
                        <p>CraftMate uses an intelligent recommendation engine that analyzes your materials inventory to suggest crafting projects that match the items you have. You can filter the recommendations based on your material preferences and skill level. The app will also provide step-by-step instructions and project tutorials to help you complete your chosen craft.</p>
                    </details>
                    <details>
                        <summary>How do I create an account on CraftMate?</summary>
                        <p>To create an account, simply download the app and follow the registration steps. You'll need to provide basic information such as your name and email address. Once your account is created, you can start adding materials to your inventory and explore project recommendations.</p>
                    </details>
                    <details>
                        <summary>Can I add or remove materials from my inventory?</summary>
                        <p>Yes! You can easily add new materials to your inventory by specifying the material name, quantity, and category. You can also edit or remove materials as needed. CraftMate allows you to organize your materials and search for specific items in your inventory.</p>
                    </details>
                    <details>
                        <summary>How can I interact with other crafters?</summary>
                        <p>CraftMate promotes community engagement by allowing users to create profiles, connect with other crafters, share photos of completed projects, and provide feedback. You can comment on or like other users' projects and follow them to stay updated on their crafting activities.</p>
                    </details>
                    <details>
                        <summary>Can I buy or sell crafting materials through CraftMate?</summary>
                        <p>Yes, CraftMate includes an e-commerce feature that enables users to buy and sell craft projects and materials. You can list your completed crafts for sale, set prices, and track your sales. The app does not handle payment transactions directly but provides a platform for messaging between buyers and sellers.</p>
                    </details>
                    <details>
                        <summary>Can I save a crafting project for later?</summary>
                        <p>Absolutely! You can save your favorite project recommendations for later. This feature allows you to come back to your saved projects whenever you're ready to start a new crafting venture.</p>
                    </details>
                    <details>
                        <summary>Is CraftMate available on all devices?</summary>
                        <p>CraftMate is available for download on Android and iOS devices. Make sure you have the latest version of the app installed to enjoy the best features.</p>
                    </details>
                </div>

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
                        <a href="#" class="icon brands fa-twitter alt"><span class="label">Twitter</span></a>
                    </li>
                    <li>
                        <a href="#" class="icon brands fa-facebook-f alt"><span class="label">Facebook</span></a>
                    </li>
                    <li>
                        <a href="#" class="icon brands fa-instagram alt"><span
                                class="label">Instagram</span></a>
                    </li>
                    <li>
                        <a href="#" class="icon brands fa-github alt"><span class="label">GitHub</span></a>
                    </li>
                    <li>
                        <a href="#" class="icon brands fa-dribbble alt"><span
                                class="label">Dribbble</span></a>
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
