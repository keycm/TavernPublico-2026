<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tavern Publico - Menu</title>
    <link rel="stylesheet" href="CSS/main.css">
    <link rel="stylesheet" href="CSS/dark-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* --- Styles for Sticky Menu Header --- */
        .menu-header {
            position: -webkit-sticky; /* Safari */
            position: sticky;
            /* !!! VERIFY THIS VALUE: Should match your main header height + body padding-top !!! */
            /* Usually 90px (header) + 4px (body padding?) = 94px, but verify */
            top: 94px;
            z-index: 990; /* Below main header (1000) but above content */
            background-color: #fff; /* Essential */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); /* Keep shadow when sticky */
            width: 100%; /* Ensure full width within container */
            /* Keep original layout properties */
            margin-bottom: 30px;
            border-radius: 10px;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        /* Dark theme override for sticky header background */
        body.dark-theme .menu-header {
             background-color: #1e1e1e;
             box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        /* --- End Sticky Menu Header Styles --- */

        /* --- ! IMPORTANT: Attempt to fix conflicting overflow --- */
        /* If the header STILL doesn't stick, try uncommenting the next line */
        /* main, .menu-section, .container { overflow: visible !important; } */


        /* --- INLINED & ADJUSTED STYLES FOR MENU PAGE (Existing styles below) --- */
        .menu-section { padding: 40px 0; background-color: #fffcf5; /* Warmer, tastier background */ }

        /* Dark Mode Background Override */
        body.dark-theme .menu-section { background-color: #121212; }

        .section-heading-v2 { margin-bottom: 40px; }

        .category-buttons-container { position: relative; width: 100%; }
        .category-buttons { display: flex; flex-wrap: wrap; gap: 8px; }

        .category-btn { background-color: #fff; color: #555; border: 1px solid #e0e0e0; padding: 10px 20px; border-radius: 50px; cursor: pointer; font-size: 0.95em; font-weight: 600; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }

        .category-btn .btn-text { margin-left: 6px; }
        .category-btn:hover { background-color: #f9f9f9; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .category-btn.active { background-color: #FFD700; color: #1a1a1a; border-color: #FFD700; box-shadow: 0 4px 10px rgba(255, 215, 0, 0.3); transform: scale(1.05); }
        .category-btn.active i { color: #1a1a1a; }

        /* Dark Theme Category Buttons */
        body.dark-theme .category-btn { background-color: #1e1e1e; border-color: #333; color: #ccc; }
        body.dark-theme .category-btn:hover { background-color: #333; }
        body.dark-theme .category-btn.active { background-color: #FFD700; color: #1a1a1a; border-color: #FFD700; }

        .search-sort { display: flex; align-items: center; gap: 20px; flex-wrap: wrap; }
        .search-bar { position: relative; }
        .search-bar input { padding: 12px 20px 12px 45px; border: 1px solid #ddd; border-radius: 50px; font-size: 1em; width: 220px; transition: all 0.3s ease; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .search-bar input:focus { border-color: #FFD700; outline: none; box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2); width: 250px; }
        .search-bar i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #aaa; font-size: 1.1em; }

        /* Dark Theme Search Bar */
        body.dark-theme .search-bar input { background-color: #1e1e1e; border-color: #333; color: #fff; }
        body.dark-theme .search-bar input:focus { border-color: #FFD700; }

        .menu-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 35px; justify-content: center; padding: 20px 0; }

        /* --- NEW CATCHY & TASTY MENU ITEM CARD STYLES --- */
        .menu-item-card {
            background-color: #fff;
            border-radius: 20px; /* More rounded */
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); /* Soft, deep shadow */
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* Bouncy transition */
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 480px;
            opacity: 0;
            transform: translateY(30px);
            visibility: hidden;
            position: relative;
        }

        .menu-item-card.is-visible {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
        }

        .menu-item-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        .menu-item-card img {
            width: 100%;
            height: 260px; /* Slightly taller */
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .menu-item-card:hover img {
            transform: scale(1.1); /* Zoom effect */
        }

        .menu-item-content {
            padding: 25px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            background: linear-gradient(to bottom, #ffffff 0%, #fafafa 100%); /* Subtle gradient */
        }

        .menu-item-card h3 {
            font-family: 'Madimi One', sans-serif; /* Catchy display font */
            font-size: 1.6em;
            font-weight: 400; /* Madimi One is bold by default, maybe 400 is better */
            color: #2c3e50; /* Darker, richer text color */
            margin: 0 0 12px 0;
            line-height: 1.2;
            letter-spacing: 0.5px;
        }

        .item-summary {
            font-family: 'Mada', sans-serif;
            font-size: 1em;
            color: #666;
            line-height: 1.6;
            margin-bottom: 25px;
            flex-grow: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-style: italic; /* Menu-style description */
        }

        .view-details-btn {
            width: 100%;
            background-color: #FFD700; /* Solid Gold */
            color: #1a1a1a;
            border: none;
            border-radius: 50px; /* Pill shape */
            padding: 14px;
            font-weight: 700;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            height: auto;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4); /* Glow effect */
        }

        .view-details-btn:hover {
            background-color: #1a1a1a;
            color: #FFD700;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            transform: translateY(-2px);
        }

        .view-details-btn:active {
            transform: scale(0.95);
        }

        /* Dark theme support for new cards */
        body.dark-theme .menu-item-card {
            background-color: #1e1e1e;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
        body.dark-theme .menu-item-content {
            background: linear-gradient(to bottom, #1e1e1e 0%, #1a1a1a 100%);
        }
        body.dark-theme .menu-item-card h3 {
            color: #f0f0f0;
        }
        body.dark-theme .item-summary {
            color: #aaa;
        }
        body.dark-theme .view-details-btn {
            background-color: #FFD700;
            color: #1a1a1a;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.2);
        }
        body.dark-theme .view-details-btn:hover {
            background-color: #fff;
            color: #1a1a1a;
        }

        /* Modal Styles */
        .modal { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); justify-content: center; align-items: center; }
        .item-modal-content { background-color: #fff; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,.2); width: 90%; max-width: 500px !important; padding: 0 !important; text-align: left; position: relative; animation: fadeIn .4s; }
        @keyframes fadeIn { from { opacity: 0; transform: scale(.95) } to { opacity: 1; transform: scale(1) } }
        .item-modal-content .close-button { position: absolute; top: 10px; right: 20px; color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer; }

        .item-modal-content img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            cursor: default;
        }

        .modal-item-details { padding: 25px; }
        .modal-item-details h2 { font-size: 2em; margin-top: 0; margin-bottom: 0; text-align: left; color: #222; line-height: 1.2; }
        .modal-item-details p { font-size: 1.1em; color: #555; line-height: 1.7; margin-bottom: 20px; }
        /* Removed .modal-price-tag */

        .swipe-indicator { display: none; }

        .image-viewer-modal {
            background-color: rgba(0,0,0,0.85); /* Darker overlay */
            z-index: 2001; /* Above the first modal */
        }
        .image-viewer-content {
            background-color: transparent;
            box-shadow: none;
            max-width: 90%;
            max-height: 90vh;
            width: auto;
            height: auto;
            padding: 0 !important;
            border-radius: 5px;
        }
        .image-viewer-close {
            color: #f1f1f1;
            font-size: 40px;
            top: 15px;
            right: 35px;
            text-shadow: 0 0 8px rgba(0,0,0,0.7);
        }

        @media (max-width: 1024px) {
            .menu-header { flex-direction: column; align-items: flex-start; }
            .search-sort { width: 100%; justify-content: space-between; }
            .search-bar { flex-grow: 1; }
            .search-bar input { width: 100%; }
        }

        @media (max-width: 768px) {
            .menu-section {
                padding-top: 25px; /* Was 40px */
            }

            .section-heading-v2 {
                margin-bottom: 15px; /* Was 20px, reduced further */
            }

            .menu-header { padding: 15px; margin-bottom: 30px; flex-direction: column; align-items: stretch; top: 94px; } /* Ensure top matches sticky value */
            .category-buttons-container { position: relative; width: 100%; }
            .category-buttons {
                display: flex;
                overflow-x: auto;
                flex-wrap: nowrap;
                padding-bottom: 15px;
                -ms-overflow-style: none;
                scrollbar-width: none;
            }
            .category-buttons::-webkit-scrollbar { display: none; }
            .category-btn { flex-shrink: 0; }

            /* --- MODIFIED SEARCH/SORT --- */
            .search-sort {
                flex-direction: row; /* Was column */
                align-items: center; /* Was stretch */
                gap: 15px;
                width: 100%; /* Ensure it takes full width */
                justify-content: flex-start;
            }
            .search-bar {
                flex-grow: 1; /* Make search bar take available space */
            }
            .search-bar input {
                width: 100%; /* Make input fill the search-bar container */
            }
            /* --- END MODIFIED --- */

            .menu-grid {
                grid-template-columns: repeat(1, 1fr); /* 1 column on mobile for better detail view */
                gap: 20px;
            }

            .menu-item-card {
                height: auto; /* Remove fixed height */
                min-height: auto;
            }
            .menu-item-card img {
                height: 180px;
            }
            .menu-item-card h3 {
                font-size: 1.2em;
            }
            .view-details-btn {
                font-size: 0.9rem;
                padding: 8px;
            }

            .swipe-indicator { display: flex; align-items: center; position: absolute; top: 50%; right: 0; transform: translateY(-50%); background-color: rgba(0,0,0,0.7); color: #fff; padding: 8px 15px; border-radius: 20px; font-size: 0.85em; z-index: 10; pointer-events: none; opacity: 1; transition: opacity 0.5s ease; }
            .swipe-indicator.hide { opacity: 0; }
        }

        /* --- NEW: Scroll to Top Button Styles --- */
        #scrollTopBtn {
            display: none; /* Hidden by default */
            position: fixed;
            bottom: 25px;
            left: 25px; /* Positioned on the left */
            z-index: 1001;
            border: none;
            outline: none;
            background-color: #FFD700; /* Gold color to match theme */
            color: #1a1a1a;
            cursor: pointer;
            padding: 0;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 22px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transition: background-color 0.3s, opacity 0.3s, transform 0.2s;
            display: none; /* Re-set to none */
            justify-content: center; /* Added for icon centering */
            align-items: center; /* Added for icon centering */
        }

        #scrollTopBtn:hover {
            background-color: #e6c200;
            transform: scale(1.05);
        }

        /* Dark theme style for the button */
        body.dark-theme #scrollTopBtn {
            background-color: #FFD700;
            color: #1a1a1a;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        body.dark-theme #scrollTopBtn:hover {
            background-color: #e6c200;
        }
        /* --- END: Scroll to Top Button Styles --- */

    </style>
</head>
<body>

    <?php
    include 'partials/header.php';
    include 'config.php';
    ?>

    <main>
        <section class="menu-section common-padding">
            <div class="container">
                <div class="section-heading-v2">
                    <div class="sub-title">Explore Our</div>
                    <div class="title-with-lines">
                        <div class="line"></div>
                        <h2 class="main-title">Delicious Menu</h2>
                        <div class="line"></div>
                    </div>
                </div>
                <div class="menu-header">
                    <div class="category-buttons-container">
                        <div class="category-buttons">
                            <button class="category-btn active" data-category="All"><i class="fas fa-list"></i><span class="btn-text">All Items</span></button>
                            <button class="category-btn" data-category="Specialty"><i class="fas fa-utensils"></i><span class="btn-text">Specialty</span></button>
                            <button class="category-btn" data-category="Appetizer"><i class="fas fa-concierge-bell"></i><span class="btn-text">Appetizer</span></button>
                            <button class="category-btn" data-category="Breakfast"><i class="fas fa-egg"></i><span class="btn-text">All Day Breakfast</span></button>
                            <button class="category-btn" data-category="Lunch"><i class="fas fa-drumstick-bite"></i><span class="btn-text">Ala Carte/For Sharing</span></button>
                            <button class="category-btn" data-category="Sizzlers"><i class="fas fa-fire-alt"></i><span class="btn-text">Sizzling Plates</span></button>
                            <button class="category-btn" data-category="Coffee"><i class="fas fa-coffee"></i><span class="btn-text">Cafe Drinks</span></button>
                            <button class="category-btn" data-category="Non-Coffee"><i class="fas fa-mug-hot"></i><span class="btn-text">Non-Coffee</span></button>
                            <button class="category-btn" data-category="Cool Creations"><i class="fas fa-blender"></i><span class="btn-text">Frappe</span></button>
                            <button class="category-btn" data-category="Cakes"><i class="fas fa-birthday-cake"></i><span class="btn-text">Cakes</span></button>
                        </div>
                        <div class="swipe-indicator">Swipe <i class="fas fa-hand-pointer"></i></div>
                    </div>
                    <div class="search-sort">
                        <div class="search-bar">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search menu...">
                        </div>
                        <!-- Sort By removed as Price sorting is removed -->
                    </div>
                </div>
                <div class="menu-grid">
                    <?php
                     if (!isset($conn) || !$conn || $conn->connect_error) { // Check if connection exists and is valid
                         include 'config.php'; // Re-include config to get $conn
                     }

                    $sql = "SELECT * FROM menu WHERE deleted_at IS NULL ORDER BY category, name";
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Create summary
                            $raw_description = $row['description'];
                            // Simple truncation. CSS line-clamp handles the visual part, but we truncate here to avoid huge HTML
                            $summary = (mb_strlen($raw_description) > 150) ? mb_substr($raw_description, 0, 150) . '...' : $raw_description;
                            $summary_html = htmlspecialchars($summary, ENT_QUOTES);

                            echo '<div class="menu-item-card"
                                    data-name="' . htmlspecialchars($row['name'], ENT_QUOTES) . '"
                                    data-image="' . htmlspecialchars($row['image']) . '"
                                    data-description="' . htmlspecialchars($row['description'], ENT_QUOTES) . '"
                                    data-category="' . htmlspecialchars($row['category']) . '">';

                            echo '  <img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                            echo '  <div class="menu-item-content">';
                            echo '    <h3>' . htmlspecialchars($row['name']) . '</h3>';
                            echo '    <p class="item-summary">' . $summary_html . '</p>';
                            echo '    <button class="view-details-btn">View Details <i class="fas fa-arrow-right"></i></button>';
                            echo '  </div>';
                            echo '</div>';
                        }
                    } else {
                        echo "<p>No menu items found.</p>";
                    }
                    // Consider closing connection if it's the last use on the page
                    // if (isset($conn)) { $conn->close(); }
                    ?>
                </div>
            </div>
        </section>
    </main>

    <button id="scrollTopBtn" title="Go to top"><i class="fas fa-arrow-up"></i></button>

    <?php
    include 'partials/footer.php';
    include 'partials/Signin-Signup.php';
    // Close connection definitively here if not needed by includes above
     if (isset($conn) && $conn) { $conn->close(); }
    ?>

    <div id="menuItemModal" class="modal">
        <div class="modal-content item-modal-content">
            <span class="close-button">&times;</span>
            <img id="modalItemImage" src="" alt="Menu Item Image">
            <div class="modal-item-details">

                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                    <h2 id="modalItemName"></h2>
                    <button id="viewFullImageBtn" class="view-details-btn" title="View full image" style="flex-shrink: 0; margin-left: 15px; width: auto; padding: 8px 15px;">
                        <i class="fas fa-search-plus"></i>
                    </button>
                </div>

                <p id="modalItemDescription"></p>
                <!-- Price tag removed -->
            </div>
        </div>
    </div>

    <div id="imageViewerModal" class="modal image-viewer-modal">
        <span class="close-button image-viewer-close">&times;</span>
        <img class="modal-content image-viewer-content" id="fullScreenImage" alt="Full screen menu item image">
    </div>

    <script src="JS/theme-switcher.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const menuItemModal = document.getElementById('menuItemModal');
            const modalName = document.getElementById('modalItemName');
            const modalImage = document.getElementById('modalItemImage');
            // Price variable removed
            const modalDescription = document.getElementById('modalItemDescription');
            const modalCloseButton = menuItemModal ? menuItemModal.querySelector('.close-button') : null;

            document.querySelectorAll('.view-details-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const card = button.closest('.menu-item-card');
                    // Check if it's the zoom button inside the modal, if so, ignore (or handle differently)
                    if (button.id === 'viewFullImageBtn') return;

                    if (!card || !menuItemModal) return;

                    modalName.textContent = card.dataset.name;
                    modalImage.src = card.dataset.image;
                    // Price setting removed
                    modalDescription.textContent = card.dataset.description;

                    menuItemModal.style.display = 'flex';
                });
            });

            if (modalCloseButton) {
                modalCloseButton.addEventListener('click', () => {
                    if (menuItemModal) menuItemModal.style.display = 'none';
                });
            }

            // --- Image Viewer Modal Logic ---
            const imageViewerModal = document.getElementById('imageViewerModal');
            const fullScreenImage = document.getElementById('fullScreenImage');
            const imageViewerCloseBtn = imageViewerModal ? imageViewerModal.querySelector('.image-viewer-close') : null;
            const viewFullImageBtn = document.getElementById('viewFullImageBtn');

            function openImageViewer() {
                if(imageViewerModal && fullScreenImage && modalImage) {
                    fullScreenImage.src = modalImage.src;
                    imageViewerModal.style.display = 'flex';
                }
            }

            if (viewFullImageBtn) {
                viewFullImageBtn.addEventListener('click', openImageViewer);
            }

            if (imageViewerCloseBtn) {
                imageViewerCloseBtn.addEventListener('click', () => {
                    if (imageViewerModal) imageViewerModal.style.display = 'none';
                });
            }

            const categoryButtonsContainer = document.querySelector('.category-buttons');
            const swipeIndicator = document.querySelector('.swipe-indicator');

            if (categoryButtonsContainer && swipeIndicator) {
                if (categoryButtonsContainer.scrollWidth > categoryButtonsContainer.clientWidth) {
                    swipeIndicator.style.display = 'flex';
                } else {
                    swipeIndicator.style.display = 'none';
                }
                categoryButtonsContainer.addEventListener('scroll', () => {
                    swipeIndicator.classList.add('hide');
                }, { once: true });
            }

            const categoryButtons = document.querySelectorAll('.category-btn');
            const searchInput = document.getElementById('searchInput');
            // Sort variables removed
            const menuGrid = document.querySelector('.menu-grid');

            const allMenuItems = Array.from(document.querySelectorAll('.menu-item-card'));

            const filterAndSort = () => {
                const activeCategoryBtn = document.querySelector('.category-btn.active');
                if (!activeCategoryBtn || !searchInput || !menuGrid) return;
                const activeCategory = activeCategoryBtn.dataset.category;
                const searchTerm = searchInput.value.toLowerCase();

                let itemsToShow = allMenuItems;

                itemsToShow.forEach(item => {
                    const isVisibleByCategory = activeCategory === 'All' || item.dataset.category === activeCategory;
                    const itemName = item.dataset.name.toLowerCase();
                    const isVisibleBySearch = itemName.includes(searchTerm);
                    item.style.display = (isVisibleByCategory && isVisibleBySearch) ? 'flex' : 'none';
                });

                // Sorting logic removed - relying on default (database) order
            };

            categoryButtons.forEach(button => {
                button.addEventListener('click', () => {
                    categoryButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    filterAndSort();
                });
            });

            if(searchInput) searchInput.addEventListener('input', filterAndSort);

            const menuItems = document.querySelectorAll('.menu-item-card');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });

            menuItems.forEach((item, index) => {
                item.style.transitionDelay = `${index * 50}ms`;
                observer.observe(item);
            });

            filterAndSort();


            // --- NEW: Scroll to Top Button JavaScript ---
            const scrollTopBtn = document.getElementById('scrollTopBtn');

            if (scrollTopBtn) {
                // Show or hide the button based on scroll position
                window.onscroll = function() {
                    scrollFunction();
                };

                function scrollFunction() {
                    if (window.scrollY > 200 || document.documentElement.scrollTop > 200) {
                        scrollTopBtn.style.display = "flex"; // Use flex to center icon
                    } else {
                        scrollTopBtn.style.display = "none";
                    }
                }

                // Scroll to top when clicked
                scrollTopBtn.addEventListener('click', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
            // --- END: Scroll to Top Button JavaScript ---

        });
    </script>
</body>
</html>