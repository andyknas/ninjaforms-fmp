# Integrating Claris FileMaker Data API with Wordpress Ninja Forms

By using a WP Hook you can easily integrate to a wordpress site.
Using Ninja Forms on submit action listener the follwoing hook will be called.

## How to integrate
1. Modify the preset user/pass of Webhook/Webhook in the Webhook database file
2. Upload the Webhook database to your FileMaker Server that has the Data API enabled
3. Update the file 'ninjaforms-fmp.php' and set the variables for hostname, user and pass to match your Webhook file setting
4. Compress the 'ninjaforms-fmp-master' folder, upload as a WordPress plugin and activate
5. Edit form and add custom WP Hook per snapshot in Images
6. Submit form as a test
7. Record will be created in Webhook database

Now you can adjust the Process Webhook script to further process the form data.

We prefer to parse a Webhook with a common table for all incoming requests. One WP Hook can be used for multiple forms. Based on form name, you can then process and parse data to other tables in FileMaker without having to sync form and database field names between the two.
