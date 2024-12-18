GROUP := "dad-group-37"
LARAVEL_VERSION := "2.1.4"
VUE_VERSION := "2.2.6"
WS_VERSION := "2.1.5"

# LARAVEL
laravel-build group=GROUP version=LARAVEL_VERSION:
    @docker build -t registry.172.22.21.107.sslip.io/{{ group }}/api:v{{ version }} \
    -f ./deployment/DockerfileLaravel ./laravel \
    --build-arg GROUP={{ group }}

laravel-push group=GROUP version=LARAVEL_VERSION:
    @docker push registry.172.22.21.107.sslip.io/{{ group }}/api:v{{ version }}

laravel:
    @echo "Connecting to Laravel Bash"
    @kubectl exec -it {{ `kubectl get pods -n dad-group-37 --no-headers=true -o name -l app=laravel-app | awk -F "/" '{print $2}'` }} -- /bin/bash

mysql:
    @echo "Connecting to MySQL Bash"
    @kubectl exec -it {{ `kubectl get pods -n dad-group-37 --no-headers=true -o name -l app=mysql | awk -F "/" '{print $2}'` }} -- /bin/bash -c "mysql memorygame" 

# VUE
vue-build group=GROUP version=VUE_VERSION:
    @docker build -t registry.172.22.21.107.sslip.io/{{ group }}/web:v{{ version }} -f ./deployment/DockerfileVue ./vue

vue-push group=GROUP version=VUE_VERSION:
    @docker push registry.172.22.21.107.sslip.io/{{ group }}/web:v{{ version }}

# WS
ws-build group=GROUP version=WS_VERSION:
    @docker build -t registry.172.22.21.107.sslip.io/{{ group }}/ws:v{{ version }} -f ./deployment/DockerfileWS ./websockets

ws-push group=GROUP version=WS_VERSION:
    @docker push registry.172.22.21.107.sslip.io/{{ group }}/ws:v{{ version }}

# KUBERNETES
pods:
    @kubectl get pods

deploy:
    @echo "Deploying..."
    @echo "Deleting previous deployment..."
    @kubectl delete -f deployment
    @echo "Applying new deployment..."
    @kubectl apply -f deployment

desc:
    @kubectl describe pods

tags:
    @kubectl get pods -o jsonpath='{range .items[*]}{"\n"}{range .spec.containers[*]}{.image}{", "}{end}{end}' | sort

logs:
    @kubectl logs -f $(shell kubectl get pods -o jsonpath='{.items[0].metadata.name}')

apply:
    @kubectl apply -f deployment

delete:
    @kubectl delete -f deployment
