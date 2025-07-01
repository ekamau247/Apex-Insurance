<?php
 if (isset($_POST['contact'])) {
        // Database connection
        $con = new mysqli("localhost", "root", "", "Apex");

        if ($con->connect_error) {
            die("Connection failed: " . $con->connect_error);
        }

        // Get form values
        $your_name =$_POST['your_name'];
        $email =$_POST['email'];
        $messege =$_POST['messege'];
        

        // Insert into database
        $sql = "INSERT INTO contact (your_name, email, messege) VALUES ('$your_name', '$email', '$messege' )";

       if ($con->query($sql) === TRUE) {
            echo "messege successful!";
        } else {
            echo "Error: " . $con->error;
        }

        $con->close();
    }
    ?>

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us | Apex Assurance</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background-color: #f4f6f8;
      color: #333;
    }

    header {
      background-color: #002b5c;
      color: white;
      padding: 20px 40px;
    }

    header h1 {
      margin: 0;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      padding: 0;
      margin: 10px 0 0;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: bold;
    }

    .contact-section {
      padding: 40px 20px;
      max-width: 800px;
      margin: auto;
      background: white;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
      margin-top: 40px;
    }

    .contact-section h2 {
      text-align: center;
      margin-bottom: 30px;
    }

    form {
      display: grid;
      gap: 20px;
    }

    input, textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 1rem;
    }

    button {
      background-color: #00cc99;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1rem;
    }

    .info {
      margin-top: 40px;
      font-size: 0.95rem;
    }

    footer {
      text-align: center;
      padding: 20px;
      background-color: #001a33;
      color: white;
      margin-top: 40px;
    }
  </style>
</head>
<body>

  <header>
    <h1>Apex Assurance</h1>
    <nav>
      <ul>
        <li><a href="Index.php">Home</a></li>
        <li><a href="REPORT/report_an_accident.php">Report</a></li>
        <li><a href="TRACK.php">Track</a></li>
        <li><a href="contact.php">Contact</a></li>
      </ul>
    </nav>
  </header>

  <section class="contact-section">
    <h2>Contact Us</h2>
    <form action="#" method="post">
      <input type="text" name="your_name" placeholder="Your Name" required />
      <input type="email" name="email" placeholder="Your Email" required />
      <textarea name="messege" rows="6" placeholder="Your Messege" required></textarea>
      <button type="submit" name="contact">Send Message</button>
    </form>

    <div class="info">
      <p><strong>Address:</strong> Apex Towers, Nairobi, Kenya</p>
      <p><strong>Email:</strong> support@apexassurance.com</p>
      <p><strong>Phone:</strong> +254 712 345 678</p>
      <p><strong>Support Hours:</strong> Mon - Fri, 8am - 5pm</p>
    </div>
  </section>

  <footer>
    <p>&copy; 2025 Apex Assurance. All rights reserved.</p>
  </footer>

</body>
</html>
