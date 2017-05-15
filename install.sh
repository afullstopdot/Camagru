while true; do
	read -p "Do you wish to install this program? " yn
	case $yn in
		[Yy]* ) mv public ..; mv app ..; mv .htaccess ..; mv index.php ..; echo 'mv complete, cd to parent and delete Camagru'; break;;
		[Nn]* ) exit;;
		* ) echo "Please answer yes or no.";;
	esac
done
