# SSecurityExtension
Security Extension for SilverStripe CMS Admins and/or Subsite Members

# Requirements:
SilverStripe 3.x

A few of my SS buddies have asked, and so here it is, the SSecurityExtension extension.
This is a class to restrict CMS access of users with ADMIN rights by an IP whitelist.
It is also compatible with the subsites module (although not required) and will
check to see if a user is a member of the subsite which they are trying to access.
In all cases, if something ain't right, the user is logged out immediately
and redirected back into the login form. No funny business or hackers allowed :-)

Installation:
unzip the module to the root of your SilverStripe site and run a dev/build

Setup:
Edit the array of allowed IP's (const ALLOWED) in the SecurityExtension class.
Maintaining the list as part of the class has worked for me for my needs, but if you have
an extensive list it would make sense to maintain/read it from an external file.

Enjoy!

#PS
suggestions welcome!
