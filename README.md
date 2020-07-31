# guide-content
Wordpress Plugin for UW-Madison Guide Content

<b>What does this plugin do?<b>
In short, it allows Wordpress users to display academic program information from within the https://guide.wisc.edu domain directly on their own website.
  
<b>How does the plugin work?<b>
1.	Once installed and activated on your Wordpress site, the plugin reads the user-specified attributes from the [guide-content] shortcode (url and tab attributes)
2.	Based on the url attribute value, the plugin loads the index.xml version of that guide.wisc.edu webpage
3.	The plugin parses the XML content and extracts the contents of the chosen tab (at that point, the content is already pre-formatted html)
4.	Finally, it prints the html contents on the Wordpress page in the location where the shortcode is placed
