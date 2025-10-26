<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>User Login and Registration</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" 
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" 
          crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <div class="login-box">
            <div class="row">

                <!-- LOGIN FORM -->
                <div class="col-md-6 login-left">
                    <h2>Login Here</h2>
                    <form action="validation.php" method="post">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="user" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>

                <!-- REGISTRATION FORM -->
                <div class="col-md-6 login-right">
                    <h2>Registration Here</h2>
                    <form action="registration.php" method="post">
                        <div class="form-group">
                            <label>Student ID</label>
                            <input type="text" name="student_id" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Class Name</label>
                            <input type="text" name="class_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="user" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Register</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</body>

</html>
