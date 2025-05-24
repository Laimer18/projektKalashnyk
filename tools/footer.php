 <div class="container-fluid">   
        <div class="row">
            <div class="col-md-12 footer">
                <p id="footer-text">
                
                	Copyright &copy; 2025 <a href="#">KalashnykKP</a>
                 
                 </p>
            </div>
        </div>
    </div> 
<!-- Cookie Consent Banner START -->
<div id="cookieConsentBanner" style="display: none; position: fixed; bottom: 0; left: 0; width: 100%; background-color: #2c3e50; color: white; padding: 15px; text-align: center; z-index: 1000; box-shadow: 0 -2px 10px rgba(0,0,0,0.2); font-size: 14px; line-height: 1.5;">
    <p style="margin: 0 0 10px 0;">Ми використовуємо файли cookie, щоб покращити ваш досвід на нашому сайті. Продовжуючи перегляд, ви погоджуєтесь на використання нами файлів cookie. <a href="/projekt1/user/view_cookies.php" style="color: #1abc9c; text-decoration: underline;">Дізнатися більше</a>.</p>
    <button id="cookieConsentAgree" style="background-color: #27ae60; color: white; border: none; padding: 8px 18px; margin-right: 10px; cursor: pointer; border-radius: 4px; font-size: 14px;">Згоден</button>
    <button id="cookieConsentDisagree" style="background-color: #c0392b; color: white; border: none; padding: 8px 18px; cursor: pointer; border-radius: 4px; font-size: 14px;">Не згоден</button>
</div>

<script>
<?php
// This PHP block is now part of the script content for apply_diff.
// tools/header.php already calls session_start(), so $_SESSION should be available.
$isLoggedIn = isset($_SESSION['user_id']);
// Fetch from session, which should be populated on login by SessionManager or by save_cookie_consent.php
$dbUserConsentStatus = $_SESSION['user_cookie_consent_status'] ?? 'pending';
?>

document.addEventListener('DOMContentLoaded', function() {
    const consentBanner = document.getElementById('cookieConsentBanner');
    const agreeButton = document.getElementById('cookieConsentAgree');
    const disagreeButton = document.getElementById('cookieConsentDisagree');
    
    // JavaScript variables from PHP
    const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
    const dbUserConsentStatus = <?php echo json_encode($dbUserConsentStatus); ?>;

    // Local cookie to manage banner visibility within the current session after a choice is made,
    // even before a page reload reflects the DB/session update.
    const localSessionChoiceCookieName = 'user_session_consent_choice';

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/; SameSite=Lax";
    }

    // Determine if banner should be shown
    // Show if: user is logged in AND their DB consent status is 'pending'
    // AND they haven't made a choice in the current session (checked by localSessionChoiceCookieName)
    const localSessionChoice = getCookie(localSessionChoiceCookieName);

    if (isLoggedIn && dbUserConsentStatus === 'pending' && !localSessionChoice) {
        if (consentBanner) consentBanner.style.display = 'block';
    } else {
        if (consentBanner) consentBanner.style.display = 'none';
    }

    function handleConsentChoice(status) {
        if (!isLoggedIn) {
            // console.log("User not logged in, cannot save consent to DB.");
            if (consentBanner) consentBanner.style.display = 'none'; // Hide banner anyway
            return;
        }

        fetch('/projekt1/user/save_cookie_consent', { // Updated to the new route
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ consent_status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // console.log('Consent saved to DB:', status);
                // Set local cookie for this session to immediately hide banner on subsequent interactions
                // Set for 0 days to make it a session cookie, or 1 day if preferred.
                setCookie(localSessionChoiceCookieName, status, 0);
                if (consentBanner) consentBanner.style.display = 'none';
                // Optionally, update the PHP-driven dbUserConsentStatus in JS if needed for immediate complex logic,
                // though a page reload would naturally pick up the session change.
                // For example: if you had JS logic that re-evaluates dbUserConsentStatus.
            } else {
                // console.error('Failed to save consent to DB:', data.message);
                // Decide how to handle error: maybe show a message to user, or just log it.
                // For now, we'll still hide the banner to avoid pestering the user.
                if (consentBanner) consentBanner.style.display = 'none';
            }
        })
        .catch(error => {
            // console.error('Error sending consent choice:', error);
            if (consentBanner) consentBanner.style.display = 'none'; // Hide on error too
        });
    }

    if (agreeButton) {
        agreeButton.addEventListener('click', function() {
            handleConsentChoice('accepted');
        });
    }

    if (disagreeButton) {
        disagreeButton.addEventListener('click', function() {
            handleConsentChoice('rejected');
        });
    }
});
</script>
<!-- Cookie Consent Banner END -->