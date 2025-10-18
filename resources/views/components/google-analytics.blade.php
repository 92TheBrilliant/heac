@if(config('services.google_analytics.id') && app()->environment('production'))
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.id') }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '{{ config('services.google_analytics.id') }}', {
    'page_title': document.title,
    'page_path': window.location.pathname
  });

  // Custom event tracking helper
  window.trackEvent = function(eventName, eventParams = {}) {
    gtag('event', eventName, eventParams);
  };

  // Track research downloads
  window.trackResearchDownload = function(researchId, researchTitle) {
    gtag('event', 'research_download', {
      'event_category': 'Research',
      'event_label': researchTitle,
      'research_id': researchId,
      'value': 1
    });
  };

  // Track research views
  window.trackResearchView = function(researchId, researchTitle) {
    gtag('event', 'research_view', {
      'event_category': 'Research',
      'event_label': researchTitle,
      'research_id': researchId
    });
  };

  // Track page views
  window.trackPageView = function(pageTitle, pagePath) {
    gtag('event', 'page_view', {
      'page_title': pageTitle,
      'page_path': pagePath
    });
  };

  // Track contact form submissions
  window.trackContactFormSubmission = function() {
    gtag('event', 'contact_form_submit', {
      'event_category': 'Contact',
      'event_label': 'Contact Form Submission'
    });
  };

  // Track search queries
  window.trackSearch = function(searchTerm, resultCount) {
    gtag('event', 'search', {
      'search_term': searchTerm,
      'result_count': resultCount
    });
  };
</script>
@endif
