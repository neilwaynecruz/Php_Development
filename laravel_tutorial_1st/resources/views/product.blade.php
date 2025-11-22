<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Product Page</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background:#f7f7f7; margin: 0; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; }
        .card {
            background: #fff; border: 1px solid #ddd; border-radius: 8px;
            padding: 16px; box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .card img { width: 100%; height: auto; border-radius: 6px; }
        .name { font-size: 22px; font-weight: bold; margin: 12px 0 6px; }
        .price { color: #159947; font-weight: bold; margin-bottom: 8px; }
        .desc { color: #555; line-height: 1.5; }
        a { display:inline-block; margin-top:12px; color:#0d6efd; text-decoration:none; }
        a:hover { text-decoration:underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <img src="{{ asset('images/sample-product.jpg') }}" alt="Sample Product">

            <div class="name">Sample Product</div>
            <div class="price">₱5000.00</div>

            <p class="desc">
                Jo Malone Wood & Sea Salt is a refreshing fragrance that captures the crispness of coastal woods and the invigorating scent of the ocean. Its blend of natural earthy and marine notes creates a serene and sophisticated aroma perfect for everyday wear.
            </p>
        </div>
    </div>
</body>
</html>
