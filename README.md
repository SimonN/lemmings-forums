# The Lemmings Forums

The backbone of <a href="https://www.lemmingsforums.net/index.php" target="_blank">www.lemmingsforums.net</a>

Powered by <a href="http://www.simplemachines.org" target="_blank">Simple Machines Forum (SMF)</a>
Copyright 2011
<a href="http://www.simplemachines.org/about/smf/license.php" target="_blank">License BSD</a>

Some files are from addons. The licences for these are in their files.

add_hooks.php should be removed on the live server. It does not need to be run when restoring from a backup;
it would need to be run if setting up the site again from scratch, discarding the old database. (It's harmless
to keep it, but doesn't need to be there.)

Database passwords
------------------

Database passwords aren't included in `Settings.php`. We should find a method to add `$db_passwd` during deployment. Other passwords can stay blank.
