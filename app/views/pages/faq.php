<?php require_once APP_ROOT . '/views/inc/header.php'; ?>

<div class="card">
    <h1 style="color: #007bff;">Frequently Asked Questions</h1>

    <div style="margin-top: 30px;">
        <h3 style="margin-bottom: 5px;">Is it free for patients to use?</h3>
        <p style="margin-top: 0; color: #555;">Yes! Patients can register, search the directory, and book appointments completely free of charge.</p>

        <h3 style="margin-bottom: 5px; margin-top: 25px;">Do I need to download an app for video calls?</h3>
        <p style="margin-top: 0; color: #555;">No. Our platform integrates Jitsi Meet directly into the browser. You can join video consultations securely from your phone or computer without installing any additional software.</p>

        <h3 style="margin-bottom: 5px; margin-top: 25px;">Can doctors use their own domain name?</h3>
        <p style="margin-top: 0; color: #555;">Currently, clinic profiles are hosted on our secure platform using a custom URL slug (e.g., <code><?= URL_ROOT ?>/clinic/view/your-clinic</code>). If you upgrade to our Premium White-Label plan, all platform branding is removed from your profile.</p>

        <h3 style="margin-bottom: 5px; margin-top: 25px;">How do the reviews work?</h3>
        <p style="margin-top: 0; color: #555;">To maintain a trustworthy directory, patients can only leave a rating and review for a clinic <em>after</em> they have successfully completed an appointment. Doctors can also privately rate patients to keep track of no-shows.</p>
    </div>
</div>

<!-- FAQ Schema.org JSON-LD -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [{
    "@type": "Question",
    "name": "Is it free for patients to use?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Yes! Patients can register, search the directory, and book appointments completely free of charge."
    }
  }, {
    "@type": "Question",
    "name": "Do I need to download an app for video calls?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "No. Our platform integrates Jitsi Meet directly into the browser. You can join video consultations securely from your phone or computer without installing any additional software."
    }
  }, {
    "@type": "Question",
    "name": "How do the reviews work?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "To maintain a trustworthy directory, patients can only leave a rating and review for a clinic after they have successfully completed an appointment."
    }
  }]
}
</script>

<?php require_once APP_ROOT . '/views/inc/footer.php'; ?>
