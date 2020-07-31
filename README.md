# guide-content
<h2>A Wordpress Plugin for UW-Madison Guide Content</h2>

<h3>What does this plugin do?</h3>
In short, it allows Wordpress users to display academic program information from within the https://guide.wisc.edu domain directly on their own website.
  
<h3>How does it work?</h3>
<ol><li>Once installed and activated on your Wordpress site, the plugin reads the user-specified attributes from the [guide-content] shortcode (url and tab attributes)</li>
<li>Based on the url attribute value, the plugin loads the index.xml version of that guide.wisc.edu webpage</li>
<li>The plugin parses the XML content and extracts the contents of the chosen tab (at that point, the content is already pre-formatted html)</li>
<li>Finally, it prints the html contents on the Wordpress page in the location where the shortcode is placed</li></ol>

<h3>How to Use in Wordpress</h3>
<ol><li>(Required) Define the url attribute. The value should correspond to the url of the Guide page whose information you want displayed on the Wordpress page (e.g. url=” https://guide.wisc.edu/undergraduate/human-ecology/consumer-science/personal-finance-bs/#text")</li>
<ul><li>The url value will work for all Undergraduate, Graduate, and Nondegree plan pages listed in Guide</li>
<li>The url used for this attribute should be the url that shows up after clicking on the tab, so it should always include a # followed by the tabname</li></ul>
<li>(Optional) Define the geneds attribute: geneds=”n”
<ul><li>This optional attribute, if set to value of “n”, will hide the General Education Requirements. This will only work on an undergraduate plan page and only for those shortcodes where tab=”requirements”, because that is the only place in Guide where General Education Requirements are displayed.</li></ul>
</ol>
Bonus side-note: If either the url or tab attribute values are invalid, nothing will be displayed on the page. 

<h3>TODO:</h3>
<ul><li>Create manual installation instructions</li>
  <li>Get approved on Wordpress.org for easier installation</li>
</ul>

