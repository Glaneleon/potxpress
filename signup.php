<!-- signup.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="assets/icons/PotXpressicon1.png" rel="icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="assets/styles/signupstyles.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dosis:wght@400;500;700&display=swap">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>

    <div class="container">
        <h2 class="mt-3">Sign Up</h2>
        <form class="p-2" action="config/signup.php" method="POST" onsubmit="return validatePasswords();">
            <div class="row mb-3">
                <div class="col-md-5">
                    <label for="fname" class="form-label">First Name:</label>
                    <input type="text" name="fname" class="form-control" required>
                </div>
                <div class="col-md-5">
                    <label for="lname" class="form-label">Last Name:</label>
                    <input type="text" name="lname" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label for="age" class="form-label">Age:</label>
                    <input type="number" name="age" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address:</label>
                <input type="text" name="address" class="form-control" required>
            </div>

            <div class="mb-3 row">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                    <small class="form-text text-muted">Please enter a valid email address.</small>
                </div>
                <div class="col-md-6">
                    <label for="mobile_number" class="form-label">Mobile Number:</label>
                    <input type="tel" name="mobile_number" class="form-control" pattern="[0-9]{11}" required>
                    <small class="form-text text-muted">Please enter a valid 11-digit mobile number starting with a zero.</small>
                </div>
            </div>

            <div class="mb-3 row">
                <div class="col-md-4">
                    <label for="username" class="form-label">Username:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label for="password" class="form-label">Password:</label>
                    <div class="password-container">
                        <input type="password" name="password" id="password" class="form-control" pattern="[A-Za-z0-9]{8,16}" title="Password must be 8-16 characters long and contain only alphanumeric characters" required>
                        <span class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="verify_password" class="form-label">Verify Password:</label>
                    <span class="password-match-icon text-success">&#10004;</span>
                        <span class="password-mismatch-icon text-danger">&#10008;</span>
                    <div class="mb-3 password-container">
                        <input type="password" name="verify-password" id="verify-password" class="form-control" required>
                        <span class="toggle-password" onclick="togglePassword('verify-password')">
                            <i class="fas fa-eye"></i>
                        </span>
                        <span id="password-match-icon"></span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Sign Up</button>
        </form>

        <div class="mt-2"><p>Have an account? <a href="login.php">Log In</a></p></div>
    </div>

    

    <!-- Bootstrap and Font Awesome JS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>

    <script>
        function togglePassword(inputId) {
            var passwordInput = document.getElementById(inputId);
            var toggleButton = document.querySelector("#" + inputId + "-toggle");
        
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleButton.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                passwordInput.type = "password";
                toggleButton.innerHTML = '<i class="fas fa-eye"></i>';
            }
        }

        // Check password and verify password match
        document.getElementById('verify-password').addEventListener('input', function () {
            var password = document.getElementById('password').value;
            var verifyPassword = this.value;

            var matchIcon = document.querySelector('.password-match-icon');
            var mismatchIcon = document.querySelector('.password-mismatch-icon');

            if (password === verifyPassword && verifyPassword !== '') {
                matchIcon.style.display = 'inline-block';
                mismatchIcon.style.display = 'none';
            } else {
                matchIcon.style.display = 'none';
                mismatchIcon.style.display = 'inline-block';
            }
        });

        function validatePasswords() {
        var password = document.getElementById('password').value;
        var verifyPassword = document.getElementById('verify-password').value;

        var matchIcon = document.querySelector('.password-match-icon');
        var mismatchIcon = document.querySelector('.password-mismatch-icon');

        if (password === verifyPassword && verifyPassword !== '') {
            matchIcon.style.display = 'inline-block';
            mismatchIcon.style.display = 'none';
            return true;  // Passwords match, allow form submission
        } else {
            matchIcon.style.display = 'none';
            mismatchIcon.style.display = 'inline-block';
            return false; // Passwords don't match, prevent form submission
        }
    }

    document.getElementById('mobile_number').addEventListener('input', function () {
        var mobileNumber = this.value;

        // Check if the mobile number starts with 0 and is exactly 11 digits
        var isValid = /^(0\d{10})$/.test(mobileNumber);

        var smallHint = document.querySelector('.form-text');

        if (isValid) {
            smallHint.textContent = 'Valid 11-digit mobile number starting with 0.';
            smallHint.style.color = 'green';
        } else {
            smallHint.textContent = 'Please enter a valid 11-digit mobile number starting with 0.';
            smallHint.style.color = 'red';
        }
    });

    document.getElementById('email').addEventListener('input', function () {
        var email = this.value;

        // Check if the email has a valid format
        var isValidEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);

        var smallHint = document.querySelector('.form-text');

        if (isValidEmail) {
            smallHint.textContent = 'Valid email address.';
            smallHint.style.color = 'green';
        } else {
            smallHint.textContent = 'Please enter a valid email address.';
            smallHint.style.color = 'red';
        }
    });
    </script>

    <script>
    function handleSignupResponse(response) {
        if (response.status === "success") {
            Swal.fire({
                icon: 'success',
                title: 'Signup Successful!',
                text: response.message,
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = 'login.php';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: response.message
            });
        }
    }

    $(document).ready(function () {
        // Inside the form submission success block
        $('form').submit(function (e) {
            e.preventDefault();

            if (validatePasswords()) {
                $.ajax({
                    type: "POST",
                    url: "config/signup.php",
                    data: $(this).serialize(),
                    success: function (response) {
                        handleSignupResponse(JSON.parse(response));
                    }
                });
            }
        });
    });
    </script>

</body>
</html>
