document.addEventListener('DOMContentLoaded', function() {

var observer = new MutationObserver(function(mutationsList) {
  for (var mutation of mutationsList) {
    if (mutation.type === 'attributes' && (mutation.attributeName === 'src' || mutation.attributeName === 'srcset')) {
      var element = mutation.target;
      var pattern = /^https:\/\/[^\/]+\/wp-content\/uploads\/\d{4}\/\d{2}\//;
		if(mutation.attributeName === 'src') {
			
			if(!checkURL(element.getAttribute('src'))) {
			         var src = element.getAttribute('src');
					  var modifiedSrc = src.replace(pattern, '');
				if(checkURL(modifiedSrc)){
					  element.src = modifiedSrc;
				   }
			}
	   } else {
		   if(!checkURL(element.getAttribute('srcset'))) {
			         var srcset = element.getAttribute('srcset');
					  var modifiedSrcset = srcset.replace(pattern, '');
			   				if(checkURL(modifiedSrcset)){
					  element.srcset = modifiedSrcset;
			   }
			   }

	   }
    }
  }
});

// Start tracking changes in the DOM
observer.observe(document, { attributes: true, attributeFilter: ['src', 'srcset'], subtree: true });

function checkURL(url) {
	var pattern = /^(https?:\/\/)?([\w.-]+)\.([a-z]{2,6}\.?)(\/[\w.-]*)*\/?$/;
  return pattern.test(url);
}
});
