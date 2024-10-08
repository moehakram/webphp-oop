<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>500 Internal Server Error</title>
<meta name="description" content="500 Internal Server Error">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
</head>
<body class="py-5" onload="javascript:loadDomain();">
<!-- Error Page Content -->
<div class="container">
    <div class="hero text-center my-4">
        <h1 class="display-5"><i class="bi bi-emoji-frown text-danger mx-3"></i></h1>
        <h1 class="display-5 fw-bold">500 Internal Server Error</h1>
        <p class="lead">The web server is returning an internal error for <em><span id="display-domain"></span></em>.
        </p>
        <p><btn onclick=javascript:reloadPage(); class="btn btn-outline-success btn-lg">Try This Page Again</a></btn>
    </div>

    <div class="content">
        <div class="row  justify-content-center py-3">
            <div class="col-md-6">
                <div class="my-5 p-5 card">
                    <h3>What happened?</h3>
                    <p class="fs-5"><?= $errors ?? "A 500 error status implies there is a problem with the web server's software causing it to malfunction." ?> </p>
                </div>
                <div class="my-5 p-5 card">
                    <h3>What can I do?</h3>
                    <p>Nothing you can do at the moment. If you need immediate assistance, please send us an email instead. We apologize for any inconvenience.</p>
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
    // CTA button actions
    function goToHomePage() {
        window.location = '/';
    }
    function reloadPage() {
        document.location.reload(true);
    }
</script>
</body>
</html>
