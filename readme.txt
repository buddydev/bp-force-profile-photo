=== BP Force Profile Photo ===
Contributors: buddydev, sbrajesh
Tags: buddypress, Profile, Avatar
Requires at least: BuddyPress 2.0
Tested up to: BuddyPress 2.9.1
Stable tag: 1.0.7
License: GPLv2 
License URI: http://www.gnu.org/licenses/gpl-2.0.html

BP Force Profile Photo plugin forces a user to upload their profile photo(avatar) before they start using the site features.

== Description ==

**BP Force Profile Photo** plugin forces a user to upload their profile photo(avatar) before they start using the site features.

= How it works:- =

It checks if a users is logged in and whether they have a profile photo or not? If they have not uploaded a profile photo, It redirects them to the Change avatar( or Change Profile Photo ) screen on their profile.
The user will only be able to use the site after they upload a profile photo. **Site Admin** user accounts are exception. They can use the site without uploading avatar.

**Important**: We prefer to support via [BuddyDev Forums]( http://buddydev.com/support/forums/) . We may not be able to assist you on WordPress.org. Please use our forum for timely support. 

== Installation ==

1. Download the zip file and extract
1. Upload `bp-force-profile-photo` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Enjoy

== Frequently Asked Questions ==

= Does This plugin works without BuddyPress =
No, It needs you to have BuddyPress Installed and activated



== Changelog ==


= 1.0.7 =
* record the changes for proper user id even when admin uploads other user's avatar

= 1.0.6 =
* Added filter to skip urls

= 1.0.2 =
* Add localization support. Test with BuddyPress 2.4.2

= 1.0.1 =
* Add support for various 3rd party social plugins to be excluded from the requirement.

= 1.0.0 =
* Initial release for BuddyPress 2.0+(Tested with BuddyPress 2.2.1)

== Other Notes ==
We appreciate your thoughts and suggestions. Please let us know your suggestions and comments on [BuddyDev Blog](http://buddydev.com/buddypress/force-buddypress-users-to-upload-profile-photo/) 