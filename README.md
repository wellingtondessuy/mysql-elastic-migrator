docker build -t mysql-elastic-migrator-image .

docker run -d -v /home/wellington/projects/mysql-elastic-migrator/:/usr/src/app --name mysql-elastic-conector mysql-elastic-migrator-image
