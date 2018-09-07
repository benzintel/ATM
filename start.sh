echo "install or start:"
read START 

if [ "$START" == "install" ]; then
	# install laravel
	echo "------------------Installing Vender Laravel------------------"
	cd laravel
	composer install

	sleep 5
fi

echo "------------------Start Docker------------------"
#start docker start
docker-compose up
