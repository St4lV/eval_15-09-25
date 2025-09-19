docker stop eval_back_ifocop_container
docker container prune -f
docker image prune -f

docker build -t eval-back-ifocop .

docker run -d -p 3002:3002 --name eval_back_ifocop_container eval-back-ifocop
