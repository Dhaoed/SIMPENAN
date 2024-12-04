<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ PAGE</title>
    <link rel="icon" type="image/x-icon" href="assets/Logo Simpenan real.png" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
            font-size: 32px;
            font-weight: 600;
        }

        header {
            background: #1A6BD0;
            padding: 15px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 30px;
        }

        .logo-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-wrapper img {
            width: 45px;
            height: auto;
        }

        .logo-wrapper h1 {
            color: white;
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        nav ul {
            display: flex;
            align-items: center;
            gap: 30px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        nav ul li a:hover {
            color: #e0e0e0;
        }

        .btn-login {
            background-color: white;
            color: #1A6BD0 !important;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: bold !important;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: #f0f0f0;
            transform: translateY(-2px);
        }

        .search-container {
            margin-bottom: 40px;
        }

        .search-box {
            width: 100%;
            padding: 16px 20px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .search-box:focus {
            outline: none;
            border-color: #1A6BD0;
            box-shadow: 0 0 0 2px rgba(26,107,208,0.2);
        }

        .search-btn {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #1A6BD0;
            cursor: pointer;
        }

        .highlight {
            background-color: #ffeb3b;
            padding: 2px;
            border-radius: 3px;
        }

        .search-results-info {
            margin: 15px 0;
            padding: 10px;
            border-radius: 5px;
            background-color: #f8f9fa;
            display: none;
        }

        .search-match {
            border-left: 4px solid #1A6BD0 !important;
        }

        .search-category-match {
            color: #1A6BD0;
            font-size: 0.9em;
            margin-left: 10px;
        }

        .faq-container {
            margin-top: 30px;
        }

        .faq-category {
            margin-bottom: 30px;
        }

        .faq-item {
            background: white;
            border-radius: 12px;
            margin-bottom: 16px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .faq-question {
            padding: 24px;
            cursor: pointer;
            position: relative;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-size: 16px;
        }

        .faq-question:hover {
            background-color: #f8f9fa;
        }

        .faq-question::after {
            content: '+';
            font-size: 24px;
            color: #666;
        }

        .faq-item.active .faq-question::after {
            content: '×';
        }

        .faq-answer {
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease-out;
            background-color: white;
        }

        .faq-item.active .faq-answer {
            padding: 0 24px 24px;
            max-height: 500px;
        }

        .faq-answer-content {
            color: #666;
            line-height: 1.6;
        }

        .accordion .card {
            border: none;
            margin-bottom: 10px;
            border-radius: 8px;
            overflow: hidden;
        }

        .accordion .card-header {
            background: #f8f9fa;
            padding: 0;
            border: none;
        }

        .accordion .btn-link {
            width: 100%;
            text-align: left;
            color: #333;
            text-decoration: none;
            font-weight: 600;
            padding: 15px 20px;
            position: relative;
        }

        .accordion .btn-link:hover {
            text-decoration: none;
            background: #f0f0f0;
        }

        .accordion .btn-link::after {
            content: '+';
            position: absolute;
            right: 20px;
            transition: transform 0.3s ease;
        }

        .accordion .btn-link:not(.collapsed)::after {
            content: '-';
        }

        .accordion .card-body {
            padding: 20px;
            background: #fff;
            color: #666;
            line-height: 1.6;
        }

        .no-results {
            text-align: center;
            padding: 20px;
            color: #666;
            display: none;
        }
        .ask-question-btn {
            background-color: #1A6BD0;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            border: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .ask-question-btn:hover {
            background-color: #1557a8;
            transform: translateY(-2px);
        }

        .question-form {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        .question-form input,
        .question-form textarea,
        .question-form select {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
        }

        .question-form label {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .form-success {
            display: none;
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .form-error {
            display: none;
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .submit-question-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .submit-question-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo-wrapper">
                <img src="assets/Logo Simpenan.png" alt="Logo SIMPENAN">
                <h1>SIMPENAN</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about_us.php">About Us</a></li>
                    <li><a href="index.php" class="btn btn-login">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="text-center mb-4">Frequently Asked Questions</h1>

        <!-- Question Submission Button -->
        <div class="text-center mb-4">
            <button class="ask-question-btn" id="showQuestionForm">
                <i class="fas fa-question-circle mr-2"></i>Ajukan Pertanyaan Baru
            </button>
        </div>

        <!-- Question Submission Form -->
        <div class="question-form" id="questionForm">
            <h3 class="mb-4">Ajukan Pertanyaan Baru</h3>
            
            <!-- Success and Error Messages -->
            <div class="form-success" id="formSuccess">
                Pertanyaan Anda telah berhasil dikirim! Kami akan segera meninjaunya.
            </div>
            <div class="form-error" id="formError">
                Maaf, terjadi kesalahan. Silakan coba lagi nanti.
            </div>

            <form id="newQuestionForm">
                <div class="form-group">
                    <label for="question">Pertanyaan</label>
                    <textarea class="form-control" id="question" name="question" rows="4" required></textarea>
                </div>

                <div class="text-right">
                    <button type="button" class="btn btn-secondary mr-2" id="cancelQuestion">Batal</button>
                    <button type="submit" class="submit-question-btn">Kirim Pertanyaan</button>
                </div>
            </form>
        </div>

         <!-- Search section yang diperbarui -->
        <div class="search-container">
            <input type="text" class="search-box" id="searchFAQ" placeholder="Cari pertanyaan (minimal 2 karakter)...">
            <button class="search-btn" id="searchButton">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <!-- Info hasil pencarian -->
        <div class="search-results-info" id="searchResults">
            <span id="resultCount">0</span> hasil ditemukan untuk "<span id="searchTerm"></span>"
        </div>

        <!-- FAQ Content -->
        <div class="faq-container">
            <!-- Kategori Umum -->
            <div class="faq-category">
                <h2 class="category-title">Pertanyaan Pertanyaan yang mungkin anda cari</h2>
                <div class="accordion" id="accordionGeneral">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h2 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne">
                                    Apa itu SIMPENAN?
                                </button>
                            </h2>
                        </div>
                        <div id="collapseOne" class="collapse" data-parent="#accordionGeneral">
                            <div class="card-body">
                                SIMPENAN adalah platform penyimpanan dan pengelolaan data yang aman dan terpercaya. Platform ini dirancang untuk membantu pengguna menyimpan dan mengelola data penting mereka secara digital dengan tingkat keamanan yang tinggi.
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" id="headingTwo">
                            <h2 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseTwo">
                                    Bagaimana cara mendaftar di SIMPENAN?
                                </button>
                            </h2>
                        </div>
                        <div id="collapseTwo" class="collapse" data-parent="#accordionGeneral">
                            <div class="card-body">
                                Untuk mendaftar di SIMPENAN, Anda dapat mengikuti langkah-langkah berikut:
                                1. Kunjungi halaman utama website SIMPENAN
                                2. Klik tombol "Login" di pojok kanan atas
                                3. Pilih opsi "Daftar Akun Baru"
                                4. Isi formulir pendaftaran dengan data yang valid
                                5. Verifikasi email Anda
                                6. Selesaikan proses pendaftaran dengan mengikuti petunjuk yang diberikan
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" id="headingThree">
                            <h2 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseThree">
                                    Apakah layanan SIMPENAN berbayar?
                                </button>
                            </h2>
                        </div>
                        <div id="collapseThree" class="collapse" data-parent="#accordionGeneral">
                            <div class="card-body">
                                SIMPENAN menyediakan beberapa tingkat layanan:
                                - Layanan dasar (Basic) tersedia secara gratis dengan fitur standar
                                - Layanan premium tersedia dengan biaya berlangganan yang menawarkan fitur tambahan seperti penyimpanan lebih besar dan dukungan prioritas
                                - Layanan enterprise tersedia untuk kebutuhan bisnis dengan harga yang dapat disesuaikan
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" id="headingFour">
                            <h2 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFour">
                                    Bagaimana keamanan data di SIMPENAN?
                                </button>
                            </h2>
                        </div>
                        <div id="collapseFour" class="collapse" data-parent="#accordionGeneral">
                            <div class="card-body">
                                SIMPENAN mengutamakan keamanan data pengguna dengan menerapkan beberapa lapisan keamanan:
                                - Enkripsi end-to-end untuk semua data
                                - Autentikasi dua faktor (2FA)
                                - Backup data regular
                                - Monitoring keamanan 24/7
                                - Sertifikasi keamanan standar industri
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" id="headingFive">
                            <h2 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseFive">
                                    Bagaimana cara menghubungi dukungan pelanggan SIMPENAN?
                                </button>
                            </h2>
                        </div>
                        <div id="collapseFive" class="collapse" data-parent="#accordionGeneral">
                            <div class="card-body">
                                Anda dapat menghubungi tim dukungan pelanggan SIMPENAN melalui beberapa cara:
                                - Email support di support@simpenan.com
                                - Live chat yang tersedia 24/7
                                - Formulir kontak di website
                                - Media sosial resmi SIMPENAN
                                Waktu respons normal adalah 1-24 jam tergantung jenis layanan yang Anda gunakan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <!-- Tampilan tidak ada hasil -->
            <div class="no-results text-center py-4">
                <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                <p class="lead">Tidak ada hasil yang ditemukan untuk pencarian Anda.</p>
                <p class="text-muted">Coba gunakan kata kunci yang berbeda atau periksa ejaan Anda.</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
     <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
    
    <script>
        $(document).ready(function() {
            // Fungsi untuk meng-highlight text
            function highlightText(text, searchTerm) {
                if (!searchTerm) return text;
                const searchTerms = searchTerm.toLowerCase().split(' ').filter(term => term.length > 0);
                let highlightedText = text;
                
                searchTerms.forEach(term => {
                    const regex = new RegExp(`(${term})`, 'gi');
                    highlightedText = highlightedText.replace(regex, '<span class="highlight">$1</span>');
                });
                
                return highlightedText;
            }

            // Fungsi untuk normalisasi text (menghapus diacritics dan mengubah ke lowercase)
            function normalizeText(text) {
                return text.toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9\s]/g, '');
            }

            // Fungsi pencarian yang ditingkatkan
            function searchFAQ() {
                const searchTerm = $('#searchFAQ').val();
                const normalizedSearchTerm = normalizeText(searchTerm);
                
                // Reset semua highlight dan tampilan
                $('.card').removeClass('search-match');
                $('.search-category-match').remove();
                
                // Jika search term terlalu pendek
                if (searchTerm.length < 2) {
                    $('.search-results-info').hide();
                    $('.no-results').hide();
                    $('.faq-category').show();
                    $('.card').show();
                    return;
                }

                let matchCount = 0;
                let searchPerformed = false;

                // Pencarian dalam cards
                $('.accordion .card').each(function() {
                    const $card = $(this);
                    const questionText = $card.find('.btn-link').text().trim();
                    const answerText = $card.find('.card-body').text().trim();
                    const normalizedQuestion = normalizeText(questionText);
                    const normalizedAnswer = normalizeText(answerText);
                    
                    // Cek apakah ada match dalam pertanyaan atau jawaban
                    const matchInQuestion = normalizedQuestion.includes(normalizedSearchTerm);
                    const matchInAnswer = normalizedAnswer.includes(normalizedSearchTerm);
                    
                    if (matchInQuestion || matchInAnswer) {
                        matchCount++;
                        $card.show().addClass('search-match');
                        
                        // Highlight text yang cocok
                        if (matchInQuestion) {
                            const highlightedQuestion = highlightText(questionText, searchTerm);
                            $card.find('.btn-link').html(highlightedQuestion);
                        }
                        if (matchInAnswer) {
                            const highlightedAnswer = highlightText(answerText, searchTerm);
                            $card.find('.card-body').html(highlightedAnswer);
                        }

                        // Buka collapse jika ada match di jawaban
                        if (matchInAnswer) {
                            $card.find('.collapse').collapse('show');
                        }
                    } else {
                        $card.hide().removeClass('search-match');
                    }
                });

                // Update tampilan hasil pencarian
                $('.search-results-info').show();
                $('#resultCount').text(matchCount);
                $('#searchTerm').text(searchTerm);

                // Toggle tampilan no results
                $('.no-results').toggle(matchCount === 0);

                // Hide kategori yang tidak memiliki hasil
                $('.faq-category').each(function() {
                    const visibleCards = $(this).find('.card:visible').length;
                    $(this).toggle(visibleCards > 0);
                });
            }

            // Event listeners
            let searchTimeout;
            $('#searchFAQ').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(searchFAQ, 300); // Debounce search
            });

            $('#searchButton').on('click', searchFAQ);

            // Reset tampilan saat form di-clear
            $('#searchFAQ').on('search', function() {
                if ($(this).val() === '') {
                    $('.card').show().removeClass('search-match');
                    $('.search-results-info').hide();
                    $('.no-results').hide();
                    $('.faq-category').show();
                    $('.collapse').collapse('hide');
                    $('.btn-link, .card-body').each(function() {
                        $(this).html($(this).text());
                    });
                }
            });
        });

        $(document).ready(function() {
            // Existing search functionality
            $("#searchFAQ").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                var found = false;
                
                $(".accordion .card").filter(function() {
                    var text = $(this).text().toLowerCase();
                    var matches = text.indexOf(value) > -1;
                    $(this).toggle(matches);
                    if (matches) found = true;
                });
                
                $(".no-results").toggle(!found);
                $(".faq-category").each(function() {
                    var category = $(this);
                    var visibleCards = category.find(".card:visible").length;
                    category.toggle(visibleCards > 0);
                });
            });

            // Question form functionality
            $("#showQuestionForm").click(function() {
                $("#questionForm").slideDown();
                $(this).hide();
            });

            $("#cancelQuestion").click(function() {
                $("#questionForm").slideUp();
                $("#showQuestionForm").show();
                $("#formSuccess, #formError").hide();
                $("#newQuestionForm")[0].reset();
            });

            $("#newQuestionForm").submit(function(e) {
                e.preventDefault();
                
                // Create form data object
                var formData = {
                    name: $("#name").val(),
                    email: $("#email").val(),
                    category: $("#category").val(),
                    question: $("#question").val()
                };

                // Send to server using AJAX
                $.ajax({
                    url: 'submit_question.php', // Create this PHP file to handle the submission
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $("#formSuccess").slideDown();
                        $("#formError").hide();
                        $("#newQuestionForm")[0].reset();
                        setTimeout(function() {
                            $("#questionForm").slideUp();
                            $("#showQuestionForm").show();
                            $("#formSuccess").hide();
                        }, 3000);
                    },
                    error: function() {
                        $("#formError").slideDown();
                        $("#formSuccess").hide();
                    }
                });
            });
        });
    </script>
</body>
</html>