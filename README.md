# BookKeeping Platform

BookKeeping Platform is a Laravel-based equipment management system for tracking organizational IT and office equipment, equipment loans, maintenance records, user assignments, and equipment history.

The project was upgraded for a DevOps deployment workflow:

- Laravel 12 application served by PHP-FPM
- Nginx web server in front of PHP-FPM
- PostgreSQL database instead of SQLite
- Docker image build for the Laravel app and Nginx
- Docker Compose orchestration for local execution
- GitHub Actions CI pipeline that publishes images to GitHub Container Registry
- Kubernetes manifests for Deployment, Service, Ingress, ConfigMap, Secret, PostgreSQL StatefulSet, PVC, migration Job, and seed Job

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
| PostgreSQL | `database` service | `bookkeeping-postgres` StatefulSet |
| Laravel config | Compose environment and `.env` | ConfigMap |
| Secrets | `.env` | Secret |
| Database storage | Docker named volume | PersistentVolumeClaim |
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

## Repository Files Added For Deployment

- `Dockerfile`: multi-stage Docker build for Composer dependencies, Vite assets, Laravel PHP-FPM, and Nginx
- `docker-compose.yml`: local three-service stack with Nginx, Laravel app, and PostgreSQL
- `docker/nginx/default.conf.template`: Nginx virtual host configuration
- `docker/php/entrypoint.sh`: waits for PostgreSQL and starts Laravel PHP-FPM
- `.github/workflows/container-images.yml`: builds and publishes Docker images to GHCR
- `k8s/`: Kubernetes manifests
- `DEPLOYMENT.md`: extra deployment notes and commands

## Environment

The app now uses PostgreSQL by default.

Important local `.env` values:

```env
APP_NAME="BookKeeping Platform"
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=bookkeeping
DB_USERNAME=bookkeeping
DB_PASSWORD=bookkeeping
```

Docker Compose overrides `DB_HOST` to `database` internally.

## Run With Docker Compose

Prerequisites:

- Docker Desktop
- Git

Build and start the stack:

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

Open the app:

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

Stop the stack and remove the PostgreSQL volume:

```bash
docker compose down -v
```

Use `down -v` only when you intentionally want to delete the local Compose database.

## Demo Users

After seeding, these users are available:

| Role | Email | Password |
| --- | --- | --- |
| Manager | `manager@example.com` | `password` |
| Employee | `test@example.com` | `password` |

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

## GitHub Actions And GHCR

The workflow at `.github/workflows/container-images.yml` builds and publishes two images:

```text
ghcr.io/stankodzeparoski/book-keeping-platform-laravel/app:latest
ghcr.io/stankodzeparoski/book-keeping-platform-laravel/nginx:latest
```

The workflow runs on pushes to:

```text
KIIUprades
KIIUpgrades
```

It can also be started manually from:

```text
GitHub repository -> Actions -> Build and Publish Container Images -> Run workflow
```

If the workflow cannot push packages, enable write permissions:

```text
Repository -> Settings -> Actions -> General -> Workflow permissions -> Read and write permissions
```

If the Kubernetes cluster cannot pull the GHCR images, make the GitHub packages public from the repository package settings.

## Kubernetes Deployment

Prerequisites:

- Docker Desktop with Kubernetes enabled
- `kubectl`
- Ingress Nginx Controller

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

Apply the Kubernetes manifests:

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
- PostgreSQL Service: `bookkeeping-postgres`
- PostgreSQL StatefulSet: `bookkeeping-postgres`
- PostgreSQL PVC: `postgres-data-bookkeeping-postgres-0`
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
bookkeeping-app       2/2 Running
bookkeeping-postgres  1/1 Running
bookkeeping-migrate   0/1 Completed
PVC                   Bound
Ingress               bookkeeping.local
```

The migration pod showing `0/1 Completed` is correct. It is a Kubernetes Job, so it runs once and exits after the migrations finish.

## Kubernetes Database Seeding

Run the seed Job:

```bash
kubectl apply -f k8s/seed-job.yaml
kubectl logs job/bookkeeping-seed -n bookkeeping-platform
```

Check it:

```bash
kubectl get job bookkeeping-seed -n bookkeeping-platform
kubectl get pods -n bookkeeping-platform
```

If you need to rerun seeding, delete and recreate the Job:

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

Alternative access with port-forwarding:

```bash
kubectl port-forward svc/bookkeeping-app 8080:80 -n bookkeeping-platform
```

Then open:

```text
http://localhost:8080
```

## Useful Kubernetes Commands

Describe the app pod:

```bash
kubectl describe pod -n bookkeeping-platform -l app=bookkeeping-app
```

View Laravel PHP-FPM logs:

```bash
kubectl logs -n bookkeeping-platform -l app=bookkeeping-app -c app
```

View Nginx logs:

```bash
kubectl logs -n bookkeeping-platform -l app=bookkeeping-app -c nginx
```

View PostgreSQL logs:

```bash
kubectl logs statefulset/bookkeeping-postgres -n bookkeeping-platform
```

Delete the Kubernetes deployment:

```bash
kubectl delete namespace bookkeeping-platform
```

Deleting the namespace removes the application resources in that namespace, including the local Docker Desktop PVC.

## Local Non-Docker Development

Docker Compose is the recommended local path for this project. If you still want to run Laravel directly on the host machine, install PHP, Composer, Node.js, npm, and PostgreSQL, then run:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
npm run dev
php artisan serve
```

The direct local app is available at:

```text
http://127.0.0.1:8000
```

## Testing

Run tests locally if PHP and Composer are installed:

```bash
php artisan test
```

Run tests inside Docker:

```bash
docker compose exec app php artisan test
```

The PHPUnit configuration uses in-memory SQLite for tests, while the application runtime uses PostgreSQL.

### Test Structure

Feature tests are located in `tests/Feature` and cover HTTP/controller-level behavior:

- `tests/Feature/Auth`: authentication, registration, password reset, and email verification tests
- `tests/Feature/Controllers/EquipmentControllerTest.php`: equipment controller workflows
- `tests/Feature/Controllers/UserControllerTest.php`: user management workflows
- `tests/Feature/Controllers/MaintenanceRecordControllerTest.php`: maintenance record workflows
- `tests/Feature/Controllers/EquipmentHistoryControllerTest.php`: equipment history workflows
- `tests/Feature/Controllers/ProfileControllerTest.php`: profile update workflows
- `tests/Feature/ProfileTest.php`: profile feature behavior

Unit tests are located in `tests/Unit` and cover isolated application logic:

- `tests/Unit/Actions/LoanEquipmentActionTest.php`
- `tests/Unit/Actions/ReturnEquipmentActionTest.php`
- `tests/Unit/Actions/RepairEquipmentActionTest.php`
- `tests/Unit/Actions/FinishRepairActionTest.php`
- `tests/Unit/Models/EquipmentObserverTest.php`

Run a specific test file:

```bash
php artisan test tests/Unit/Actions/LoanEquipmentActionTest.php
```

Run a specific test by filter:

```bash
php artisan test --filter=LoanEquipmentActionTest
```

## Troubleshooting

Clear old Laravel cache files:

```bash
docker compose exec app php artisan optimize:clear
```

Rebuild Docker images from scratch:

```bash
docker compose down
docker compose build --no-cache app nginx
docker compose up -d --force-recreate
```

If GitHub Actions cannot find `Dockerfile`, check that the workflow uses the correct path for this repository layout:

```yaml
context: ./BookKeepingPlatform
file: ./BookKeepingPlatform/Dockerfile
```

If Kubernetes reports `ImagePullBackOff`, verify that the image names in `k8s/deployment.yaml`, `k8s/migration-job.yaml`, and `k8s/seed-job.yaml` match the GHCR images and that the packages are public or pull credentials are configured.

## Project Structure

```text
BookKeepingPlatform/
  app/
    Actions/                 Business logic for loan, return, repair, and finish-repair workflows
    Enums/                   Equipment category, condition, and status enums
    Http/
      Controllers/           Request handlers for equipment, users, profile, auth, history, and maintenance
      Middleware/            Manager middleware
      Requests/              Form request validation classes
    Models/                  Eloquent models
    Observers/               Model observers for automatic status/history updates
    Providers/               Laravel service providers
    View/                    Blade layout components
  bootstrap/                 Laravel bootstrap files
  config/                    Laravel configuration files
  database/
    factories/               Model factories used by tests and seeders
    migrations/              PostgreSQL-compatible schema migrations
    seeders/                 Sample users, equipment, loans, and maintenance records
  docker/
    nginx/                   Nginx container configuration
    php/                     PHP-FPM entrypoint script
  k8s/                       Kubernetes namespace, config, secrets, app, database, ingress, jobs
  public/                    Public web root
  resources/
    css/                     Tailwind/CSS source
    js/                      JavaScript source
    views/                   Blade templates
  routes/                    Web, console, and auth routes
  storage/                   Runtime storage ignored by Git except placeholders
  tests/
    Feature/                 Feature/controller/auth tests
    Unit/                    Unit/action/observer tests
  .github/workflows/         GitHub Actions CI workflow
  Dockerfile                 Multi-stage app and Nginx image build
  docker-compose.yml         Local Nginx, Laravel, PostgreSQL stack
  composer.json              PHP dependencies and scripts
  package.json               Vite/Tailwind frontend dependencies
```

## Version

Current deployment-focused version: `1.0.0`

Last updated: June 2026
