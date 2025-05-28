<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Certificate</title>
<style>
  /* @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap'); */
  body {
    margin: 0;
    padding: 32px;
    background-color: #ffffff; /* white background */
    font-family: 'Times New Roman', serif;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
  }
  .certificate {
    background: white;
    position: relative;
    max-width: 875px;
    width: 100%;
    padding: 32px 48px 200px 48px; /* increased bottom padding for more spacing */
    box-sizing: border-box;
    text-align: center;
    z-index: 0;
  }
  /* Outer blue border */
  .certificate::before {
    content: "";
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    border: 20px solid #001080; /* blue border */
    pointer-events: none;
    z-index: -2;
  }
  /* Inner orange border */
  .certificate::after {
    content: "";
    position: absolute;
    top: 20px; left: 20px; right: 20px; bottom: 20px;
    border: 10px solid #e67e22; /* orange border */
    pointer-events: none;
    z-index: -1;
  }
  .logo {
    display: block;
    margin: 0 auto 24px auto;
    width: auto;
    height: 100px;
    object-fit: contain;
  }
  h1 {
    color: #e6a623;
    font-weight: 600;
    /* font-size: 2rem; */
    margin: 0 0 8px 0;
  }
  h2 {
    font-weight: 600;
    /* font-size: 2rem; */
    margin: 0 0 16px 0;
  }
  p {
    margin: 0 0 8px 0;
    font-weight: 600;
    font-size: 1rem;
    color: #000;
  }
  .recipient {
    font-weight: 600;
    font-size: 1rem;
    margin: 0 0 16px 0;
  }
  .small-text {
    font-size: 16px;
    margin: 0 0 32px 0;
    font-weight:lighter;
  }
  .photo {
    width: 110px;
    height: 150px;
    /* object-fit: cover; */
    background-image: url({{ public_path( 'storage/'. optional($photo)->doc_path) }});
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    margin: 0 auto;
    position: absolute;
    bottom: 100px;
    left: 50%;
    transform: translateX(-50%);
  }
  .footer-container {
    position: absolute;
    bottom: 100px;
    right: 48px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    font-size: 14px;
    color: #000;
    min-width: 180px;
  }
  .footer-container strong {
    text-decoration: underline;
    font-weight: 700;
    margin-bottom: 2px;
  }
</style>
</head>
<body>
  <div class="certificate" role="main" aria-label="Certificate of Completion">
      <img
        src="{{ public_path('assets/images/logos/logo-avi.svg') }}"
        alt="Astra Visteon Indonesia"
        class="logo"
        width="200"
        height="60"
        style="margin-top:20px"
      />
      <h1>SERTIFIKAT {{ Str::upper($type) }}</h1>
      <h2>PT. ASTRA VISTEON INDONESIA</h2>
      <p>DIBERIKAN KEPADA:</p>
      <h2>{{ Str::upper($kontrak->user->fullname) }}</h2>
      <p class="small-text">
        TELAH MENYELESAIKAN PROGRAM {{ Str::upper($type)  }} DI<br />
        PT. ASTRA VISTEON INDONESIA
      </p>
      
      <div
        class="photo"
      >
      </div>
      <div class="footer-container">
          <div>Citeureup, {{$kontrak->end_date->isoFormat('D MMMM Y') }}</div>
          <div style="margin-top: 40px;">
            <strong>SAFITRI</strong><br>
            HR,GA&amp;EHS Dept Head
          </div>
      </div>
  </div>
</body>
</html>