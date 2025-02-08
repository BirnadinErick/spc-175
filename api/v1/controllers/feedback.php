<?php function feedback()
{
    // for now, v just ignore the feedback ;)
    ensure_request_method("POST");
?>
    <button id="feedback-button" class="inline-block px-6 py-2 bg-transparent text-spc-gold font-bold rounded-full border-2 border-spc-gold hover:bg-spc-gold/10 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 text-center text-lg cursor-not-allowed" disabled> Sent! </button>
<?php } ?>