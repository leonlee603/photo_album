# photo_album
php photo album

This is a photo album created by using PHP, MYSQL, HTML, CSS & Bootstrap.

For normal user, they can view the photo album and leave comment on any photo they like.</br>
The number of view and the comment of the album and photo will show on the page.</br></br>

====================</br>
The pages for normal user:</br>
====================</br>
1.)index.php - This is the index page which consits of a navbar and created albums, user can click on any album to further discover the photos in that album.</br>
2.)albumDisplay.php - This page will show the user all photos in a particular album, the page will show the basic information of that album including the album's name, album's description, number of photos and number of views.</br>
3.)albumPhoto.php - This page will show the selected photo in a larger version, also there is a list of small photos below it showing the other photos which are in the same album. Moreover, there is a comment section on the right bottom corner which let user to leave comment on the photo.</br>
4.)allPhoto.php - This page will show all the photos uploaded to the site ordered by the uploaded date.</br></br>

For admin user, this album allow you to upload photos, create new album, edit the album information and photo description.</br>
Also, admin can delete the comments in any photo.Admin can logout on any page by clicking the logout button on the navbar.</br></br>
================</br>
The pages for admin:</br>
================</br>
1.)connMysql.php - This script is use for connecting the MYSQL database and create the mysqli object for interacting with the database.</br>
2.)login.php - When user click on the login button on navbar, they will be directed to this page to login to the admin page. If the username and password are correct, the page will create a php session value to recognize the user as admin.</br>
3.)admin.php - This is the page admin will be directed to if they login successfully. Admin can create album, edit album or delete album in this page.</br>
4.)adminCreate.php - When admin wants to create an album, the form in this page will collect the album information. All input will be processed by trim and filter_var function before store into database.</br>
5.)adminEdit.php - Admin can edit any existing album in this page, including album's information, photo's information, delete selected or delete any comments on particular photo.</br>
6.)adminComment.php - This page will show all comments on a particular photo, admin can delete any of them.</br>



Responsive

http://leonlee603.freecluster.eu/
