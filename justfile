
GROUP := "dad-group-37"
LARAVEL_VERSION := "2.0.0"
VUE_VERSION := "2.0.1"
WS_VERSION := "2.0.0"

kubectl-apply:
    kubectl apply -f deployment

kubectl-update:
    kubectl delete -f deployment
    kubectl apply -f deployment

#LARAVEL
laravel-build group=GROUP version=LARAVEL_VERSION:
    docker build -t registry.172.22.21.107.sslip.io/{{group}}/api:v{{version}} \
    -f ./deployment/DockerfileLaravel ./laravel \
    --build-arg GROUP={{group}}
    
laravel-push group=GROUP version=LARAVEL_VERSION:
    docker push registry.172.22.21.107.sslip.io/{{group}}/api:v{{version}}

#VUE
vue-build group=GROUP version=VUE_VERSION:
    docker build -t registry.172.22.21.107.sslip.io/{{group}}/web:v{{version}} -f ./deployment/DockerfileVue ./vue

vue-push group=GROUP version=VUE_VERSION:
    #replace version in ./deployment/kubernetes-vue.yaml
    docker push registry.172.22.21.107.sslip.io/{{group}}/web:v{{version}}

#WS
ws-build group=GROUP version=WS_VERSION:
    docker build -t registry.172.22.21.107.sslip.io/{{group}}/ws:v{{version}} -f ./deployment/DockerfileWS ./websockets

ws-push group=GROUP version=WS_VERSION:
    docker push registry.172.22.21.107.sslip.io/{{group}}/ws:v{{version}}

#KUBERNETES
pods:
    kubectl get pods

deploy:
    kubectl delete -f deployment
    kubectl apply -f deployment

desc:
    kubectl describe pods

tags:
    kubectl get pods -o jsonpath='{range .items[*]}{"\n"}{range .spec.containers[*]}{.image}{", "}{end}{end}' |\
    sort

logs:
    kubectl logs -f $(shell kubectl get pods -o jsonpath='{.items[0].metadata.name}')