<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>עיריית רמת הוד</title>
  <link rel="stylesheet" href="/assets/css/styles.css" />
  <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon" />
</head>
<body>
  <header class="header">
    <div class="header__buttons">
      <a href="index.html" class="header__link">הודעות אחרונות</a>
    </div>
    <div class="header__title">רמת הוד</div>
  </header>

  <main>
    <section class="messages">
      <h2 class="messages__heading">הודעות</h2>

      <button class="messages__toggle-form" id="toggle-form" aria-expanded="false">
        הוסף הודעה חדשה
        <span id="plus-minus-symbol">+</span>
      </button>

      <div class="messages__form-container" id="new-message-form-container" style="display: none;">
        <h3>הוסף הודעה חדשה</h3>
        <form id="new-message-form" class="form" novalidate>
          <label for="name">שם המפרסם:</label>
          <input type="text" id="name" name="name" required />

          <label for="email">דוא"ל המפרסם:</label>
          <input type="email" id="email" name="email" required />

          <label for="message">תוכן ההודעה:</label>
          <textarea id="message" name="message" required></textarea>

          <button type="submit">שלח</button>
        </form>
        <div id="form-response" class="form__response"></div>
      </div>

      <ul id="messages-list" class="messages__list"></ul>
    </section>
  </main>

  <script src="/assets/js/main.js"></script>
</body>
</html>
