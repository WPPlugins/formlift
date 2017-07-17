=== FormLift for Infusionsoft Web Forms===
Contributors: trainingbusinesspros
Tags: Infusionsoft, Optin, Form, Editor, Official, FormLift, Web Form
Requires at least: 4.5
Donate link: https://formlift.net
Tested up to: 4.8
Stable tag: 6.4.12
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Import Infusionsoft Web Forms into WordPress and easily customize their style. Display with short-codes.

== Description ==

Import your Infusionsoft web forms into WordPress and use over 45 different customizable options to make your forms
conform to the style of your website. Customize the looks of text fields, radio buttons, drop downs, submit buttons!
That alone makes this a great tool, but it also comes with a host of other automation improving features.

Forms will auto populate when sent information from Infusionsoft Emails, or if a lead recently submitted another form on your site.

Forms will validate emails, names, phone numbers, and required fields so failed submissions are not taken to the hosted Infusionsoft form.

FormLift will track the impressions vs. submissions right in your WP dashboard.

Personalize your WP thank-you pages with easy short-codes to display user data quick and easy.

For all the details and documentation for FormLift please visit the [plugin homepage](https://formlift.net).

= Unlicensed Features =
* Over 45 global styling options
* Forms auto populate
* Complex validation algorithm
* Conversion Rate Tracking
* Personalized Page Short-codes
* Import form Infusionsoft API
* Customizable Date Picker

= Premium Features =
* Conditional Redirect Creator Tool
* Google reCAPTCHA integration
* Individual Form Styling
* Conditional Form Display Tool

If you're looking to get even more out of FormLift. You can go premium and unlock the conditional redirect tool.
You can create quick conditional logic, no coding knowledge required to send leads to different thank-you pages based on their
submissions.

Plus...

Hit spam hard with the google reCAPTCHA integration.

You can individual style each individual web form to best suit your page's needs!

Do you need time sensitive forms? Choose date ranges or max submission numbers to limit if your form displays or not!

[Want to go premium?](https://formlift.net/purchase/)

== Installation ==

This section describes how to install the plugin and get it working.

Method 1:
    1. Upload via Plugins -> Add New page
    2. Install and Activate
    3. Start using!

Method 2:
    1. Upload to wp-content/plugins/
    2. extract .zip file contents
    3. Go to All Plugins page
    4. Activate and start using.

== Frequently Asked Questions ==

= What PHP level is required =
PHP 7.1.4 is recommended, however 5.6 and up works fine. Below 5.6 is at your own risk...

= Will this work with my existing Infusionsoft Web Forms in my campaigns? =
YES! All you need to do is import them and they will work as if they were regular infusionsoft forms.

= Is there support? =
Yes, I try to answer as many questions as I can. However paid subscriptions will be prioritized.

= Is it compatible with other Infusionsoft based plugins?
Yes, The Gravity Forms Infusionsoft Add-On, Memberium, and the new Infusionsoft Official Web Form Plugin, and Thrive Leads are all tested as compatible.
If an error arises it is likely on part of another plugin and not FormLift.

== Screenshots ==

1. Simple Color Coded HTML editor for those who like a little extra control.

2. Over 45 different styling options to customize! (button options shown)

3. Form validation to protect your forms from spam!

4. Auto population feature to help your automation kick in!

5. Nice customizable html date picker so your clients can pick the correct date for once.

6. Condition Redirect Tool to send leads where they need to go! (Premium)

7. Conditional display tool so no one submits forms past their expiry date. (Premium)

8. Personalized short-codes to make your leads feel welcome on your site!

== Changelog ==
= 6.4.12 =
1. TimeZone was not being set on a successful form submission, now it does. Just saying, infusionsoft did not make the search to do this easy at all, so your welcome for figuring it out.

= 6.4.11 =
1. Oops, forgot to change the formatting of the preview form in the settings page causing an error loading the color-picker.

= 6.4.10 =
1. Javascript loading issue of Recaptcha box

= 6.4.9 =
1. Small Bug fixes including Apostrophe's in error messages causing form load error
2. Fail safe optimization of form code
3. Added transparency option to all color options!

= 6.4.8 =
1. Fixed tracking date not setting properly

= 6.4.7 =
1. Some files disappeared randomly causing a downtime in API integration. They have been replaced.
2. Chanced some logic syntax to follow standards
3. Added better handling of exceptions thrown by the Infusionsoft SDK

= 6.4.6 =
1. More stable conversion tracking, some conversion rates may be skewed towards lower end results
2. Small auto-fill bug fix.

= 6.4.5 =
1. Make Redirects sortable for ease of use.

= 6.4.4 =
1. Require PHP 5.6 or higher to work

= 6.4.3 =
1. To new logic conditions added to the premium redirect builder. "Starts With" & "Ends With".

= 6.4.2 =
1. Minor bug fixes
2. Removed self hosted update feature
3. Ability to copy settings from another form.

= 6.4 =
1. Major update there's too much to cover. Please see the plugin homepage for more information on recent updates!

= 5.8.0 =
1. Added functionality of cookie-ing user data on form submission
2. Auto-fills based on cookied user data
3. Cookies user data that is passed through URL params
4. Conditions on auto filling form data now appears in the Settings tab of Formlift Defaults
5. Added a Redirect making metabox
    - Create Redirects based on Dropdowns and Radio Buttons
    - Use the thank you page URL as the thank you page URL in Infusionsoft
6. Added the ability to change the Placeholder colour pf text fields

= 5.5 =
1. Added a User Manual with specific instruction on how to setup lead source tracking in infusionsoft and auto populate fields
2. Added campaigns, a custom taxonomy that allows users to associated multiple web-forms with a specific campaign so directly compare conversions in case they are split testing multiple landing pages.
3. Changed the Remove Labels option to a yes/no drop down selection.
4. Re-added checkboxes to the formLift columns in admin panel.

= 5.0 =
1. Restructured code to move away from functional to object oriented.
2. Decreased code size dramatically
3. Removed live updates to preview when options are changed
4. Removed Modals pending further work.
5. Added Ajax Based Conversion tracking.
6. Required fields have been moved back to the main editing area

= 4.8 =
1. Fixed Fatal error where script wasn't firing on Safari

= 4.7 =
1. Massive UI changes
2. jQuery Color Picker is now included for all color areas to make selecting colors easier
3. live Updates to form preview based on input
4. the required fields area has been moved to the preview metabox to ensure people see it and set them.
5. required fields are now displayed as their associated label.

= 4.5 =
1. Includes new CodeMirror Library to improve the readability and editability of HTML code!

= 4.0 =
1. Modals have been introduced in limited functionality. BETA testing only, so use at your own risk.
    -Updates include:
        - A button shortcode that activates a modal
        - A modal shortcode, automatically includes the form so there is no need to place both the modal and the form shortcode on a page.
        - Copy buttons in the EDIT form area.
2. The User interface has been remodeled to improve the learning curve and increase the intuitiveness of the software.
    -Changes include:
        -dropdown tabs for different styling options both in the defaults area
        -dropdown tabs for different styling options both in the create form area
        -better labelling of fields and sections
3. The validation algorithm has again been lightened to improve speed.
4. The errors no longer appear under the fields to improve space usage and mobile friendliness, and now appear under the form in a list of errors format.
5. The radio button error has be removed and will now use the default missing field error
6. Date support! Date fields carried over from Infusionsoft will be reformatted and have a DATE picker installed so you can choose dates with a UI
7. If you decide to do so, the following fields will all have REQUIRED support. Password, Date, Number, Text, Textarea, Select, checkbox, radio

= 3.8 =

1. Backend scalability has been improved
2. Default Settings have been tweaked a bit.
3. Preparations for introducing further implementation. Hint hint... Modals are coming soon.

= 3.7.6 =

1. The validation was a bit loose and causing unexpected checking when parsing the form. Validation is now much more specific giving more variability

= 3.7.5 =

1. Overhauled form validation, again...
2. Over hauled the way required fields are required, you can now select which are required and which are not using checkboxes.
3. The Email field will be required by default, to protect the user and to avoid spam.
4. Button alignment is now a dropdown and no longer a radio button. Added some stuff to the instructions
5. Added a quick function to make `<textarea>` tags behave well in6the `form_code` area
6. form processing time is now a bit faster on the front end. But as slowed down in the editing area due to new options.

= 3.7 =

1. Added automatic updates!

= 3.6.2 =

1. Fixed bug that wouldn't allow you to submit pform post without filling out preview form fields...
2. Added new functions to handle validation.

= 3.6.1 =

1. Changed all function name calls to associate with the prefix flp_ (form lift pro)
2. Deactivates LITE version on activation to not cause conflicts between the two.
3. Added an instructions page to make the user experience slightly easier.

= 3.6 =

1. Rewrote recognition algorithm to include global functions to increase page loading speed.
2. Added preview forms to Edit form pages
3. Cleaned up code and fixed minor bugs.

= 3.5.1 =

Added a style option to align the submit button.

= 3.5 =

First public release version