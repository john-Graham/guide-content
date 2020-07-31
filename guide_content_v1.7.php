<?php
/*
 * @wordpress-plugin
 * Plugin Name:  Guide Content
 * Plugin URI:    https://github.com/mack0331/guide-content
 * Description:   Access Guide (guide.wisc.edu) content via CourseLeaf API/XML. For use on UW-Madison academic program websites.
 * Version:   1.7
 * Author:   Eric MacKay (Inspired by the work of Nathan Fetter)
 * Author URI:    https://github.com/mack0331
 * Text Domain:
 * Domain Path:
 */

//Disallow access to altering the PHP code from anywhere outside of Wordpress
if ( ! defined( 'ABSPATH' ) ) exit;

/*When the shortcode "[guide_content]" is in a text field, it will call the guide_content function below.
The shortcode must also include the url attribute. Example: [guide_content url="https://guide.wisc.edu/undergraduate/education/art/art-bs/#text]*/
add_shortcode( 'guide_content', 'guide_content' );

//Single function that is called, and evaluated separately, for each [guide_content] shortcode found on a Wordpress page
function guide_content( $atts, $post ){
    extract(shortcode_atts(array('url' => '', 'geneds' => 'y'), $atts)); //Extract the shortcode attribute values
    $url = str_replace('index.html','',trim($atts['url'])); //url attribute value (required)
    $geneds = trim($atts['geneds']); //geneds attribute value (optional)

    //Find the string to the right of the # in the url attribute to get the selected tab
    $lookup_tab = strpos($url, "#");
    $selected_tab = substr($url, $lookup_tab + 1);

    //Remove the #tab value from the end of the url
    $url = str_replace("#".$selected_tab,"",$url);

    //Set the selected_plan as the xml version of the Guide page (public access to this XML is, effectively, the entirety of the CourseLeaf-provided API)
    $selected_plan = $url.'index.xml';     

    /*Counting the backslashes in $selected_plan URL to determine if this is a plan or subplan. Plan-level directories have 7 backslashes, subplans have 8.
    This convention will never change because changing it would break Guide, as the URL is used as a key*/
    if ( substr_count($selected_plan, '/') == 8 ){
       $subplan = true;
    }

    //Grabs the XML from the selected plan page/tab comob in Guide and loads it into a DOMDocument to be parsed
    $xmlDoc = new DOMDocument();
    $xmlDoc->load($selected_plan);
    $x = $xmlDoc->documentElement;
    
    //Define the array that will hold the HTML returned from the XML
    $content_array= array();

    //Loop through all XML elements on selected Guide page, push HTML into content_array 
    foreach ( $x->childNodes AS $item ) {
        if ( $item->nodeName == $selected_tab && preg_match('/[a-zA-Z]/', $item->nodeValue) == true ){
            $tab = $item->nodeName;
            $content = '<div id="' . $item->nodeName . 'container" class="tab_content" role="tabpanel">'.str_replace('target="_blank"','', $item->nodeValue).'</div>';
            $content  = str_replace('href="/', 'href="https://guide.wisc.edu/', $content);
            $temp_array = array("tab" => $tab, "content" => $content);
            array_push($content_array, $temp_array);
        }
    }

    //XML is parsed and HTML values for that tab is stored in this array
    $content_array = array_column($content_array, 'tab', 'content'); 

    //Push the tab (HTML) contents to the $courseleaf_parsed variable for further parsing below
    $courseleaf_parsed  = array_search($selected_tab, $content_array);

    /*If Requirements Tab is selected, and if the selected plan is a non-certificate undergraduate plan (excluding named options), and if geneds = 'n',
     then hide the Gen Ed shared content (headers and paragraph text) from the Requirements Tab output*/
    if ( 'requirementstext' == $selected_tab && strpos($selected_plan, 'undergraduate') == true && strpos($selected_plan, 'certificate') !== true && $subplan == false && $geneds == 'n' ){
        $courseleaf_parsed = str_replace('name="requirementstext">University General Education Requirements', 'style="display: none;">',$courseleaf_parsed);
        $courseleaf_parsed = str_replace('name="requirementstext">University Degree Requirements', 'style="display: none;">',$courseleaf_parsed);
        $courseleaf_parsed = str_replace('</h2> <p>All undergraduate students','</h2> <p style="display: none;">', $courseleaf_parsed);        
        $courseleaf_parsed = str_replace('class="sc_sctable tbl_generaleducationrequirements"', 'style="display: none;"', $courseleaf_parsed);
    }

    //Must hide the 'On this Page' title and content because the links would not work or be accurate in all shortcode uses
        $courseleaf_parsed = str_replace('class="otp-title"', 'style="display: none;"',$courseleaf_parsed); //Hides the title
        $courseleaf_parsed = str_replace('onthispage"','" style="display: none;"',$courseleaf_parsed); //Hides the content
        $courseleaf_parsed = str_replace('class="view-choice','style="display: none; class="',$courseleaf_parsed); //Hides the "View as List" and "View as Grid" links

    //Replace Guide code-bubbles with hyperlinks that search for the courses in Guide
    $courseleaf_parsed = preg_replace('/<span class="code_bubble" data-code-bubble="(.*)">(.*)<\/span>/U', '<span><a href="https://guide.wisc.edu/search/?search=$1">$2</a></span>', $courseleaf_parsed);

    //Shift the Header Sizes down one size across the board. It appears Guide custom styling uses some wonky/nonstandard sizes that require this down-shift in UW Child Theme
    $courseleaf_parsed = str_replace('<h4','<h5 ',$courseleaf_parsed);
    $courseleaf_parsed = str_replace('</h4> ','</h5> ',$courseleaf_parsed);
    $courseleaf_parsed = str_replace('<h3','<h4 ',$courseleaf_parsed);
    $courseleaf_parsed = str_replace('</h3> ','</h4> ',$courseleaf_parsed);
    $courseleaf_parsed = str_replace('<h2','<h3 ',$courseleaf_parsed);
    $courseleaf_parsed = str_replace('</h2> ','</h3> ',$courseleaf_parsed);

    //Print all of the results onto the page! 
    return $courseleaf_parsed;

} //End function

?>