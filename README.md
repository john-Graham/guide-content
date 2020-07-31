# guide-content
<h2>Wordpress Plugin for UW-Madison Guide Content</h2>

<h3>What does this plugin do?</h3><br>
In short, it allows Wordpress users to display academic program information from within the https://guide.wisc.edu domain directly on their own website.
  
<h3>How does it work?</h3><br>
1.	Once installed and activated on your Wordpress site, the plugin reads the user-specified attributes from the [guide-content] shortcode (url and tab attributes)
2.	Based on the url attribute value, the plugin loads the index.xml version of that guide.wisc.edu webpage
3.	The plugin parses the XML content and extracts the contents of the chosen tab (at that point, the content is already pre-formatted html)
4.	Finally, it prints the html contents on the Wordpress page in the location where the shortcode is placed

<h3>How to Use in Wordpress</h3><br>
<ol><li>(Required) Define the url attribute. The value should correspond to the url of the Guide page whose information you want displayed on the Wordpress page (e.g. url=” https://guide.wisc.edu/undergraduate/human-ecology/consumer-science/personal-finance-bs/")
<ul><li>The url value will work for all Undergraduate, Graduate, and Nondegree plan pages listed in Guide</li></ul>
<ul><li>This will still work even if ‘index.html’ is appended to the end of the url value set by the user</li></ul></li>
<ol><li>(Required) Define the tab attribute. The value should correspond to the specific tab on the right side of the chosen Guide page (url). This plugin is designed to display the contents of one tab per shortcode, but users can include as many shortcodes on a page as they need. (e.g.  tab ="How to Get in”)
<ul><li>Neither case nor spacing matter (because humans) so tab=”HOW TO GET IN” will work the same as tab=”HoWtOgEtIn”</li></ul></li>
<ol><li>(Optional) Define the geneds attribute: geneds=”n”
<ul><li>This optional attribute, if set to value of “n”, will hide the General Education Requirements. This will only work on an undergraduate plan page and only for those shortcodes where tab=”requirements”, because that is the only place in Guide where General Education Requirements are displayed.</li></ul></li>

Bonus side-note: If either the url or tab attribute values are invalid, nothing will be displayed on the page. 

<h3>TODO:</h3>
<ul><li>Create manual installation instructions</li>
  <li>Get approved on Wordpress.org for easier installation</li>
</ul>

