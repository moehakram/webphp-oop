<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>404 Not Found</title>
    <meta name="description" content="404 Not Found">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>

<body class="py-5" onload="javascript:loadDomain();">
    <!-- Error Page Content -->
    <div class="container">
        <div class="hero text-center my-4">
            <h1 class="display-5"><i class="bi bi-emoji-dizzy text-danger mx-3"></i></h1>
            <h1 class="display-5 fw-bold">404 Not Found</h1>
            <p class="lead">We couldn't find what you're looking for on <em><span id="display-domain"></span></em>.
            </p>
            <a>
                <button onclick=javascript:goToHomePage(); class="btn btn-outline-success btn-lg">Go to Homepage</button>
            </a>
        </div>

        <div class="content">
            <div class="row  justify-content-center py-3">
                <div class="col-md-6">
                    <div class="my-5 p-5 card">
                        <h3>What happened?</h3>
                        <p class="fs-5"><?= $message ?? "A 404 error status implies that the file or page that you're looking for could
                            not be found." ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function loadDomain() {
            var display = document.getElementById("display-domain");
            display.innerHTML = document.domain;
        }
        
        function goToHomePage() {
            window.location = '/';
        }
    </script>
</body>

</html>