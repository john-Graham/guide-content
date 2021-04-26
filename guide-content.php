<?php
/*
 * @wordpress-plugin
 * Plugin Name:  Guide Content
 * Plugin URI:    https://github.com/mack0331/guide-content
 * Description:   Access Guide (guide.wisc.edu) content via CourseLeaf API/XML. For use on UW-Madison academic program websites.
 * Version:   1.8.4
 * Author:   Eric MacKay
 * Author URI:    https://github.com/mack0331
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
//Disallow access to altering the PHP code from anywhere outside of Wordpress
if ( ! defined( 'ABSPATH' ) ) exit;

//When the shortcode "[guide_content]" is added to a Wordpress page, it will call the single function below. The shortcode must also include the "url" attribute value.
add_shortcode( 'guide_content', 'guide_content' );

//Single function that is called, and evaluated separately, for each [guide_content] shortcode found on a Wordpress page
function guide_content( $atts, $post ){
    //Extract all of the shortcode attribute values
    extract(shortcode_atts(array('url' => '', 'after' => '', 'before' => '', 'exact' => '', 'udr' => '', 'grad' => '', 'adjust' => '', 'geneds' => ''), $atts)); 
    $url = str_replace('index.html','',trim($atts['url'])); //url attribute value (required)
    $after = trim($atts['after']); //(optional)
    $before = trim($atts['before']); //(optional)
    $exact = trim($atts['exact']); //(optional)
    $udr = trim($atts['udr']); //(optional)
    $grad = trim($atts['grad']); //(optional)
    $adjust = trim($atts['adjust']); //(optional)
    $geneds = trim($atts['adjust']); //(optional) -- deprecated

    //geneds was deprecated in 1.7, this line serves to approximate the behavior of what geneds="y" used to do
    if ( $geneds == 'y' ) {
        $udr = 'y';
    }

    //Break apart the 'adjust' attribute for each ; to allow for multiple size/style adjustments set by the user
    $adjust_array = explode(';',str_replace(' ','',$adjust));

    //Determine public URL for the plugin directory (for use in locating the mortarboard.png file)
    $plugin_url = plugin_dir_url( __FILE__ );

    //Determine root path for Catalog pages based on URL attribute value used in the Shortcode (for use in having plugin work in non-UW-Madison CAT sites)
    $inst_string = explode('/', $url);
    $institution = $inst_string[0].'//'.$inst_string[2].'/';

    //Find the string to the right of the # in the url attribute to get the selected tab
    $lookup_tab = strpos($url, '#');

    //If no tab specified, return the Overview content (#text node-value), else set the selected tab to the text found after the # in the "url" attribute value
    if ( empty($lookup_tab) !== false ) {
        $selected_tab = 'text';
    } else {
        $selected_tab = substr($url, $lookup_tab + 1);
    }

    //Remove the # and tab value from the end of the url
    $url = str_replace('#'.$selected_tab,'',$url);

    //Set the selected_plan as the xml version of the CAT page (public access to this XML is, effectively, the entirety of the CourseLeaf-provided API)
    $selected_plan = $url.'index.xml';     
    
    //Grabs the XML from the page specified in the "url" shortcode attribute loads it into a DOMDocument to be parsed
    $xmlDoc = new DOMDocument();
    if ( @$xmlDoc->load($selected_plan) !== false ) {
        $xmlDoc->load($selected_plan);
        $x = $xmlDoc->documentElement;

        //Define the array that will hold the HTML returned from the XML
        $content_array= array();

        //Loop through all XML elements on selected Guide page, push HTML into content_array 
        foreach ( $x->childNodes AS $item ) {
            if ( $item->nodeName == $selected_tab && preg_match('/[a-zA-Z]/', $item->nodeValue) !== false ){
                $tab = $item->nodeName;
                $content = '<div id="' . $item->nodeName . 'container" class="tab_content" role="tabpanel">'.str_replace('target="_blank"','', $item->nodeValue).'</div>';
                $content  = str_replace('href="/', 'href="'.$institution, $content);
                $content  = str_replace('<img ', '<img style="display: none;"', $content); //Hides all images
                $content  = str_replace('<span class="title visual">', '<span style="display: none;" class="title visual"', $content); //Hides all image-related titles
                $temp_array = array("tab" => $tab, "content" => $content);            
                array_push($content_array, $temp_array);
            }
        }

        //XML is parsed and HTML values for that tab is stored in this array
            $content_array = array_column($content_array, 'tab', 'content'); 

        //Push the tab (HTML) contents to the $courseleaf_parsed variable for further parsing below
            $courseleaf_parsed  = array_search($selected_tab, $content_array);

        //ADJUST ATTRIBUTE: Complete any 'adjusts' specified via the 'adjust' attribute
        foreach ( $adjust_array as $adjust_pair ) {
            $adjust_pair_array = explode('-',$adjust_pair);
            foreach ( $adjust_pair_array as $key=>$adjustment) { //for each adjust entry in the adjust attribute, make the requested substitutions
                if ( isset($adjust_pair_array[$key]) ) {
                    $courseleaf_parsed = str_replace('<'.$adjust_pair_array[0],'<'.$adjust_pair_array[1],$courseleaf_parsed);
                    $courseleaf_parsed = str_replace('</'.$adjust_pair_array[0].'> ','</'.$adjust_pair_array[1].'> ',$courseleaf_parsed);
                } else {
                // end of array reached--no more adjusts to complete
                }
            }
        }

        //BEFORE ATTRIBUTE: Get all of the content before, but not including, the specific H2 header
        if ( !empty($before) && empty($exact) ) {
            $before = str_replace(')', '\\)',str_replace('(', '\\(', $before));
            $courseleaf_after = preg_replace('#(.*)('.$before.'<\/h2>)(.*?)#is', '$2', $courseleaf_parsed);
            $courseleaf_parsed = ''.str_replace($courseleaf_after, '', $courseleaf_parsed);
            $courseleaf_parsed = str_replace('\\(','(',str_replace('\\)',')',$courseleaf_parsed));
        }

        //AFTER ATTRIBUTE: Get all of the content after, and including, the specific H2 header
        if ( !empty($after) && empty($exact) ){
            $after = str_replace(')', '\\)',str_replace('(', '\\(', $after));
            $courseleaf_after = preg_replace('#(.*)('.$after.'<\/h2>)#is', '$3', $courseleaf_parsed, -1, $count);
            $courseleaf_after = preg_replace('#(<\/h2>)(.*?)#is', '</h2>', $courseleaf_after);
            $courseleaf_parsed = str_replace('\\(','(',str_replace('\\)',')',$courseleaf_after));
        }

        //EXACT ATTRIBUTE: Get only the exact content between the specific H2 header and the H2 header that follows
        if ( !empty($exact) && empty($before) && empty($after) ){
            $exact = str_replace(')', '\\)',str_replace('(', '\\(', $exact));
            $courseleaf_parsed = preg_replace('#(.*)('.$exact.'<\/h2>)#is', ' $3', $courseleaf_parsed, -1, $count);
            $courseleaf_parsed = preg_replace('#(headerid="(.*)<\/h2>)(.*?)#imU', ' ></h2>', $courseleaf_parsed);
            $courseleaf_parsed = str_replace('\\(','(',str_replace('\\)',')',$courseleaf_parsed));
        }

        //Insert the Mortarboard Symbol (to indicate courses meeting Gen Ed req) where appropriate, but only if the "University General Education Requirements" is on the page
        if ( $selected_tab == 'requirementstext' && strpos($courseleaf_parsed, '* The mortarboard symbol') !== false ) {
            $courseleaf_parsed = str_replace('* The mortarboard symbol appears','* The mortarboard symbol (<img src="'.$plugin_url.'img/mortarboard.png" height="20" width="20" alt="Mortarboard Symbol">) appears',$courseleaf_parsed); 
            $courseleaf_parsed = str_replace('<i class="fa fa-graduation-cap" aria-hidden="true"></i>','<img src="'.$plugin_url.'img/mortarboard.png"  height="20" width="20" alt="Mortarboard Symbol">',$courseleaf_parsed); 
        }

        //UDR ATTRIBUTE: Automatically hide the University Degree Requirements Section unless udr = 'y'
        if ( $selected_tab == 'requirementstext' && strpos($selected_plan, 'undergraduate') !== false && strpos($selected_plan, 'certificate') !== true && $udr != 'y') {
            $courseleaf_parsed = str_replace('name="requirementstext">University Degree Requirements', 'style="display: none;">',$courseleaf_parsed);
            $courseleaf_parsed = str_replace('<tr class="even firstrow"><td class="column0">Total Degree</td>','<tr style="display: none;">', $courseleaf_parsed);
            $courseleaf_parsed = str_replace('<tr class="odd"><td class="column0">Residency</td>','<tr style="display: none;">', $courseleaf_parsed);
            $courseleaf_parsed = str_replace('<tr class="even last lastrow"><td class="column0">Quality of Work</td>','<tr style="display: none;">', $courseleaf_parsed);
        }  

        //GRAD ATTRIBUTE: Select any one of the three standard areas on the Grad Requirements tabs to display, without the other 2 showing up
        if ( $selected_tab == 'requirementstext' && strpos($selected_plan, '/graduate') !== false && strpos($selected_plan, 'certificate') !== true ) {
            $mode = 'MODE OF INSTRUCTION';
            $curr = 'CURRICULAR REQUIREMENTS';
            if ( strpos($courseleaf_parsed, '<h3>REQUIRED COURSES') !== false ) {
                $courses = 'COURSES REQUIRED';
            } else {
                $courses = 'REQUIRED COURSES';
            }

            if ( $grad == 'mode' ) {
                $courseleaf_parsed = preg_replace('#(.*)(<h3>'.$mode.')#is', '$3', $courseleaf_parsed, -1, $count);
                $courseleaf_parsed = preg_replace('#(<h3><strong>'.$curr.'<\/strong><\/h3>)(.*?)#imU', '', $courseleaf_parsed, -1, $count);
            } 
            if ( $grad == 'curr' ) {
                $courseleaf_parsed = preg_replace('#(.*)(<h3><strong>'.$curr.'<\/strong><\/h3>)#is', '$3', $courseleaf_parsed, -1, $count);
                $courseleaf_parsed = preg_replace('#(<h3>'.$courses.')(.*?)#imU', '', $courseleaf_parsed, -1, $count);
            }
            if ( $grad == 'courses' ) {
                $courseleaf_parsed = preg_replace('#(.*)(<h3>'.$courses.')#is', '$3', $courseleaf_parsed, -1, $count);
                $courseleaf_parsed = preg_replace('#(headerid="(.*)<\/h2>)(.*?)#imU', ' ></h2>', $courseleaf_parsed);
            }
        }  

        //If the chosen "url" attribute is for a Requirements tab, and if that plan has a named options grid, then reduce the size of the H3 named options to P
        //Default Guide styling adjusts the H3 'View as List' size for the named options, this change is to help it match Guide styling better, without using CSS
        if ( strpos($courseleaf_parsed, '<div class="visual-sitemap grid">') !== false ) {
        $courseleaf_parsed = str_replace('"><h3>', '"><p>', $courseleaf_parsed);
        $courseleaf_parsed = str_replace('"></a><h3>', '"><p>', $courseleaf_parsed);
        $courseleaf_parsed = str_replace('</h3></a></li>', '</p></a></li>', $courseleaf_parsed);
        }

        //Must hide the 'On this Page' title and content because the links would not work
        $courseleaf_parsed = str_replace('class="otp-title"', 'style="display: none;"',$courseleaf_parsed); //Hides the title
        $courseleaf_parsed = str_replace('onthispage"','" style="display: none;"',$courseleaf_parsed); //Hides the content
        $courseleaf_parsed = str_replace('class="view-choice','style="display: none; class="',$courseleaf_parsed); //Hides the "View as List" and "View as Grid" links

        //Remove content with class = "hidden" because they would otherwise be hidden based on custom Guide styling
        $courseleaf_parsed = str_replace('class="hidden','style="display: none;" class="hidden',$courseleaf_parsed); 
        $courseleaf_parsed = str_replace('class="sctablehead hidden', 'style="display: none;" class="sctablehead hidden', $courseleaf_parsed);

        //Default Guide styling makes the 'areaheader' and 'areasubheader' in Course Lists bold--need to re-add the bold since no styling is inherited
        $courseleaf_parsed = str_replace('class="courselistcomment area','style="font-weight:bold" class="courselistcomment area',$courseleaf_parsed); 
        $courseleaf_parsed = str_replace('class="listsum"><td ','style="font-weight:bold" class="listsum"><td style="font-weight:bold" ',$courseleaf_parsed); 

        //Default Guide styling makes the 'areasubheader' in Course Lists italicized--need to re-add the italics since no styling is inherited
        $courseleaf_parsed = str_replace('class="odd areasubheader','style="font-style:italic" class="odd areasubheader',$courseleaf_parsed); 
        $courseleaf_parsed = str_replace('class="even areasubheader','style="font-style:italic" class="even areasubheader',$courseleaf_parsed); 

        //Replace code-bubbles with hyperlinks that search for the courses in Catalog
        $courseleaf_parsed = preg_replace('/<span class="code_bubble" data-code-bubble="(.*)">(.*)<\/span>/U', '<span><a href="'.$institution.'search/?P=$1">$2</a></span>', $courseleaf_parsed);
        
        //Due to the variety of ways in which the "before" "after" and "exact" shortcodes may be placed within a single or multiple Wordpress Page Elements, there was a potential for a DIV to be left open or closed erroneously. 
        //This section checks each instance of the [guide_content] shortcode and makes sure that it is appropriately contained within a DIV so that it does not affect any other pgae content
        if ( substr_count($courseleaf_parsed, '<div') > substr_count($courseleaf_parsed,'</div>') ){
            $courseleaf_parsed .= '</div>';
        }
        else if ( substr_count($courseleaf_parsed, '<div') < substr_count($courseleaf_parsed,'</div>') ){
            $courseleaf_parsed = '<div>'.$courseleaf_parsed;
        }

        //Print all of the results onto the page!
        return $courseleaf_parsed;

    } //End if no DOM document found

} //End function

?>