<!DOCTYPE html>
<html>
<head>
  <title>Devi Eye hospitals</title>
  <style>
    body{
      font-family: 'Roboto', sans-serif;
      font-size: 13px;
      font-weight: normal;
    }
    footer{
        position: fixed; 
        bottom: -60px; 
        left: 0px; 
        right: 0px;
        height: 50px; 
        color: #000;
        text-align: center;
    }
    .text-center{
        text-align: center;
    }
    .text-right, .text-end{
      text-align: right;
    }
    .table, .no-border{
      border: none;
    }
    .bordered{
      border: 1px solid #262525;
    }
    .border-0{
      border: 0;
    }
    th, td{
      border: 1px solid #262525;
      padding: 5px;
    }
    .mt-10{
      margin-top: 10px;
    }
    .mt-30{
      margin-top: 30px;
    }
    .mt-50{
      margin-top: 50px;
    }
    .mt-100{
      margin-top: 100%;
    }
    .mb-50{
      margin-bottom: 50px;
    }
    .h-50>tr>td{
      height: 50px;
    }
    .fw-bold{
      font-weight: bold;
    }
    .text-danger{
      color: red;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row">
      <div class="col text-center">
        <img src="./backend/assets/images/logo/devi-pdf-logo.png" width='10%'/>
      </div>
    </div>
    @yield("pdfcontent")
  </div>
</body>
</html>