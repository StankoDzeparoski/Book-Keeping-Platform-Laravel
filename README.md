# BookKeeping Platform

BookKeeping Platform is a Laravel-based equipment management system for tracking organizational IT and office equipment, equipment loans, maintenance records, user assignments, and equipment history.

The project was upgraded for a DevOps deployment workflow:

- Laravel 12 application served by PHP-FPM
- Nginx web server in front of PHP-FPM
- PostgreSQL database instead of SQLite
- Docker Compose orchestration for local execution
- GitHub Actions CI pipeline that publishes images to GitHub Container Registry
- Kubernetes manifests for app deployment, ingress, config, secrets, PostgreSQL primary, PostgreSQL read replica, PVCs, migration Job, and seed Job

If you cloned the GitHub repository root and the Laravel project is inside `BookKeepingPlatform/`, run:

```bash
cd BookKeepingPlatform
```

All commands below assume you are inside the Laravel project directory.

## Architecture

| Component | Docker Compose | Kubernetes |
| --- | --- | --- |
| Laravel PHP-FPM | `app` service | `app` container in `bookkeeping-app` Deployment |
| Nginx | `nginx` service | `nginx` container in `bookkeeping-app` Deployment |
| PostgreSQL primary | `database` service | `bookkeeping-postgres-primary` StatefulSet |
| PostgreSQL read replica | Not used locally | `bookkeeping-postgres-replica` StatefulSet |
| Laravel config | Compose environment and `.env` | ConfigMap |
| Secrets | `.env` | Secret |
| Database storage | Docker named volume | PVC per PostgreSQL StatefulSet |
| External access | `localhost:8080` | Ingress `bookkeeping.local` |
| Migrations | `docker compose exec` | Kubernetes Job |
| Seed data | `docker compose exec` | Kubernetes Job |
| Image build | Local Docker / GitHub Actions | Pulled from GHCR |

## Main Features

- Equipment CRUD management
- Equipment loan and return workflow
- Equipment repair and maintenance tracking
- Equipment assignment history
- User management
- Laravel Breeze authentication
- Role fields for Manager and Employee users
- PostgreSQL-backed sessions, cache, queue, and application data

## User Roles

### Manager

Managers have full access to the equipment management workflow:

- Create, edit, view, and delete equipment
- View all equipment in the system
- Loan equipment to any user
- Return equipment
- Mark equipment for repair
- Finish equipment repair
- View equipment history
- Create, view, edit, and delete maintenance records
- Create, view, edit, and delete users
- Assign user roles
- View assigned equipment for each user

### Employee

Employees have limited access focused on their own equipment usage:

- View available equipment
- View equipment currently assigned to them
- Loan available equipment to themselves
- Return equipment they have loaned
- View and update their own profile

Employees do not manage users, maintenance records, or full equipment history.

## Technology Stack

- Laravel 12
- PHP 8.3
- Blade and Tailwind CSS
- PostgreSQL 16
- Nginx
- Docker
- Docker Compose
- GitHub Actions
- GitHub Container Registry
- Kubernetes
- Ingress Nginx Controller

## Run With Docker Compose

Prerequisites:

- Docker Desktop
- Git

Build and start the local stack:

```bash
docker compose build app nginx
docker compose up -d
```

Run migrations:

```bash
docker compose exec app php artisan migrate --force
```

Seed the database:

```bash
docker compose exec app php artisan db:seed --force
```

Reset and seed from scratch:

```bash
docker compose exec app php artisan migrate:fresh --seed --force
```

Open:

```text
http://localhost:8080
```

Show that Docker Compose is working:

```bash
docker compose ps
docker compose logs app --tail=50
docker compose logs nginx --tail=50
docker compose exec app php artisan migrate:status
```

Stop the stack:

```bash
docker compose down
```

Stop and delete the local Compose database volume:

```bash
docker compose down -v
```

## Demo Users

After seeding, these users are available:

| Role | Email | Password |
| --- | --- | --- |
| Manager | `manager@example.com` | `password` |
| Employee | `test@example.com` | `password` |

## GitHub Actions And GHCR

The workflow at `.github/workflows/container-images.yml` builds and publishes:

```text
ghcr.io/stankodzeparoski/book-keeping-platform-laravel/app:latest
ghcr.io/stankodzeparoski/book-keeping-platform-laravel/nginx:latest
```

If the Laravel app is nested inside `BookKeepingPlatform/` in the GitHub repository, the workflow must use:

```yaml
context: ./BookKeepingPlatform
file: ./BookKeepingPlatform/Dockerfile
```

The workflow can be run manually from:

```text
GitHub repository -> Actions -> Build and Publish Container Images -> Run workflow
```

## Kubernetes Deployment

Prerequisites:

- Docker Desktop with Kubernetes enabled
- `kubectl`
- Ingress Nginx Controller
- Public or pull-accessible GHCR images

Enable Kubernetes:

```text
Docker Desktop -> Settings -> Kubernetes -> Enable Kubernetes
```

Verify the cluster:

```bash
kubectl config use-context docker-desktop
kubectl get nodes
```

Install Ingress Nginx Controller:

```bash
kubectl apply -f https://raw.githubusercontent.com/kubernetes/ingress-nginx/controller-v1.15.1/deploy/static/provider/cloud/deploy.yaml
kubectl wait --namespace ingress-nginx --for=condition=ready pod --selector=app.kubernetes.io/component=controller --timeout=180s
```

Apply the manifests:

```bash
kubectl apply -k k8s
```

The manifests create:

- Namespace: `bookkeeping-platform`
- ConfigMap: `bookkeeping-app-config`
- Secret: `bookkeeping-app-secret`
- Deployment: `bookkeeping-app`
- Service: `bookkeeping-app`
- Ingress: `bookkeeping-app`
- PostgreSQL primary Service: `bookkeeping-postgres`
- PostgreSQL replica Service: `bookkeeping-postgres-replica`
- PostgreSQL primary StatefulSet: `bookkeeping-postgres-primary`
- PostgreSQL read-replica StatefulSet: `bookkeeping-postgres-replica`
- PostgreSQL primary PVC: `postgres-data-bookkeeping-postgres-primary-0`
- PostgreSQL replica PVC: `postgres-replica-data-bookkeeping-postgres-replica-0`
- Migration Job: `bookkeeping-migrate`

Check the deployment:

```bash
kubectl get pods -n bookkeeping-platform
kubectl get svc -n bookkeeping-platform
kubectl get ingress -n bookkeeping-platform
kubectl get pvc -n bookkeeping-platform
kubectl logs job/bookkeeping-migrate -n bookkeeping-platform
```

Expected state:

```text
bookkeeping-app                         2/2 Running
bookkeeping-postgres-primary-0          1/1 Running
bookkeeping-postgres-replica-0          1/1 Running
bookkeeping-migrate                     0/1 Completed
primary PVC                             Bound
replica PVC                             Bound
Ingress                                 bookkeeping.local
```

## Verify PostgreSQL Primary And Read Replica

Check that both database pods exist:

```bash
kubectl get pods -n bookkeeping-platform -l role=primary
kubectl get pods -n bookkeeping-platform -l role=replica
```

Check the primary:

```bash
kubectl exec statefulset/bookkeeping-postgres-primary -n bookkeeping-platform -- sh -c 'PGPASSWORD="$POSTGRES_PASSWORD" psql -U "$POSTGRES_USER" -d "$POSTGRES_DB" -c "select pg_is_in_recovery();"'
```

Expected result on the primary:

```text
pg_is_in_recovery
-------------------
f
```

Check the read replica:

```bash
kubectl exec statefulset/bookkeeping-postgres-replica -n bookkeeping-platform -- sh -c 'PGPASSWORD="$POSTGRES_PASSWORD" psql -U "$POSTGRES_USER" -d "$POSTGRES_DB" -c "select pg_is_in_recovery();"'
```

Expected result on the replica:

```text
pg_is_in_recovery
-------------------
t
```

Check replication status from the primary:

```bash
kubectl exec statefulset/bookkeeping-postgres-primary -n bookkeeping-platform -- sh -c 'PGPASSWORD="$POSTGRES_PASSWORD" psql -U "$POSTGRES_USER" -d "$POSTGRES_DB" -c "select application_name, state, sync_state from pg_stat_replication;"'
```

## Kubernetes Database Seeding

Run the seed Job:

```bash
kubectl apply -f k8s/seed-job.yaml
kubectl logs job/bookkeeping-seed -n bookkeeping-platform
```

If you need to rerun seeding:

```bash
kubectl delete job bookkeeping-seed -n bookkeeping-platform
kubectl apply -f k8s/seed-job.yaml
kubectl logs job/bookkeeping-seed -n bookkeeping-platform
```

Reset and seed the Kubernetes database from the app container:

```bash
kubectl exec deploy/bookkeeping-app -n bookkeeping-platform -c app -- php artisan migrate:fresh --seed --force
```

## Access The Kubernetes App

For Ingress access on Docker Desktop, add this line to your Windows hosts file:

```text
127.0.0.1 bookkeeping.local
```

Hosts file path:

```text
C:\Windows\System32\drivers\etc\hosts
```

Open:

```text
http://bookkeeping.local
```

Alternative access:

```bash
kubectl port-forward svc/bookkeeping-app 8080:80 -n bookkeeping-platform
```

Then open:

```text
http://localhost:8080
```

## Testing

Run all tests:

```bash
php artisan test
```

Run tests inside Docker:

```bash
docker compose exec app php artisan test
```

The PHPUnit configuration uses in-memory SQLite for tests, while the application runtime uses PostgreSQL.

### Test Structure

Feature tests are located in `tests/Feature`:

- `tests/Feature/Auth`: authentication, registration, password reset, and email verification tests
- `tests/Feature/Controllers/EquipmentControllerTest.php`
- `tests/Feature/Controllers/UserControllerTest.php`
- `tests/Feature/Controllers/MaintenanceRecordControllerTest.php`
- `tests/Feature/Controllers/EquipmentHistoryControllerTest.php`
- `tests/Feature/Controllers/ProfileControllerTest.php`
- `tests/Feature/ProfileTest.php`

Unit tests are located in `tests/Unit`:

- `tests/Unit/Actions/LoanEquipmentActionTest.php`
- `tests/Unit/Actions/ReturnEquipmentActionTest.php`
- `tests/Unit/Actions/RepairEquipmentActionTest.php`
- `tests/Unit/Actions/FinishRepairActionTest.php`
- `tests/Unit/Models/EquipmentObserverTest.php`

Run a specific test:

```bash
php artisan test tests/Unit/Actions/LoanEquipmentActionTest.php
```

## Project Structure

```text
BookKeepingPlatform/
  app/
    Actions/
    Enums/
    Http/
      Controllers/
      Middleware/
      Requests/
    Models/
    Observers/
    Providers/
    View/
  bootstrap/
  config/
  database/
    factories/
    migrations/
    seeders/
  docker/
    nginx/
    php/
  k8s/
  public/
  resources/
    css/
    js/
    views/
  routes/
  storage/
  tests/
    Feature/
    Unit/
  .github/workflows/
  Dockerfile
  docker-compose.yml
  composer.json
  package.json
```

## Version

Current deployment-focused version: `2.1.0`

Last updated: June 2026
