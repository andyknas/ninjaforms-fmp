# Integrating Claris FileMaker Data API with Wordpress Ninja Forms

By using a WP Hook you can easily integrate to a wordpress site.
Using Ninja Forms on submit action listener the follwoing hook will be called.

## How to integrate
Modify the preset user/pass of Webhook/Webhook in the Webhook database file
Upload the Webhook database to your FileMaker Server that has the Data API enabled
Update the file 'ninjaforms-fmp.php' and set the variables for hostname, user and pass to match your Webhook file setting
Compress the 'ninjaforms-fmp-master' folder, upload as a WordPress plugin and activate
Edit form and add custom WP Hook per snapshot in Images
Submit form as a test
Record will be created in Webhook database

Now you can adjust the Process Webhook script to further process the form data.

We prefer to parse a Webhook with a common table for all incoming requests. One WP Hoook can be used for multiple forms. Based on form name, you can then process and parse data to other tables in FileMaker without having to sync form and database field names between the two.
