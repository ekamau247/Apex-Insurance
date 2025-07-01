
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Apex Assurance | report_an_accident.php
  </title>
  <style>
    body {
      font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
      margin: 0;
      background-color: #f4f6f9;
      color: #333;
    }

    header {
      background-color: #002b5c;
      color: white;
      padding: 20px 40px;
    }

    header h1 {
      margin: 0;
      font-size: 2em;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      padding: 0;
      margin: 20px 0 0;
    }

    nav li {
      display: inline;
    }

    nav a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      transition: color 0.3s;
    }

    nav a:hover {
      color: #ffc107;
    }

    .track-section {
      max-width: 600px;
      margin: 50px auto;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .track-section h2 {
      text-align: center;
      color: #002b5c;
      margin-bottom: 20px;
    }

    .track-section form {
      display: flex;
      flex-direction: column;
    }

    .track-section label {
      margin-bottom: 10px;
      font-weight: bold;
    }

    .track-section input[type="text"] {
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    .track-section input[type="submit"] {
      background-color: #002b5c;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    .track-section input[type="submit"]:hover {
      background-color: #00408a;
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
        <li><a href="home.html">Home</a></li>
        <li><a href="report_an_accident.php">Report</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li><a href="report_an_accident.php">Report an Accident</a></li>
        <li><a href="track.html">Track Your Claim</a></li>
      </ul>
    </nav>
  </header>

  <section class="Report_an_accident">
    <h2>Report an accident</h2>
    <form method="POST" action="report_an_accident.php">
      <label for="Report">Report:</label>
      <input type="text" name="Report_id="Report" placeholder="e.g. RP12345" required>
      <input type="submit" value="Check Status">
    </form>
  </section>

  <footer>
    <p>&copy; 2025 Apex Assurance. All rights reserved.</p>
  </footer>

</body>
</html>
