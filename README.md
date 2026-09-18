# Jobstep CRM Monorepo


## Development 
1. Download a database dump into the `databaseDump/` folder and run `docker compose up -d` from the project root.
2. Add the following to your hosts file:
```
127.0.0.1 dev.crm.jobstep.com
127.0.0.1 dev.js.jobstep.com
127.0.0.1 dev.join.jobstep.com
127.0.0.1 dev.api.pa.jobstep.com
127.0.0.1 dev.pma.jobstep.com
```
## Deploying apps
For every application in the `src` directory, there are two Github Workflows: `Deploy to staging` and `Deploy to production`.
These workflows need to be ran manually on a specific commit.
The workflow will build the necessary Docker image and restart the containers in the cluster.
### Deploying CRM
Because the CRM app uses semantic versioning, it's deployment pipeline differs from the rest.
#### Deploying a new version
1. Ensure you have increased the version number in the following files:
    1. `src/crm/includes/functions.php` - function `getCopyright`
    2. `kubernetes/staging/legacy-crm/legacy-crm.yaml`
    3. `kubernetes/production/legacy-crm/legacy-crm.yaml`
2. Merge the changes into the master branch
3. Create a new release and tag, pointing the tag at the merge commit
4. Run the `CRM - Deploy to [Staging|Production]` Github Action to deploy the code to the cluster

#### Deploying without a version increase
1. Merge the necessary code into the master branch without changing any version numbers in the application code
2. Delete the latest release and the tag it was using
3. Recreate the deleted release and tag, pointing the tag at the merge commit
4. Run the `Deploy to Staging` Github Action on the created tag
   This will overwrite the existing Docker image for this specific version
   Note: there is no need to run both `Deploy to Staging` and `Deploy to Production` actions
5. Run the `Restart [Staging|Production]` Github Action to deploy the code to the cluster

## Kubernetes
### Generating Kubernetes secrets
You might have noticed that both the `kubernetes/staging` and `kubernetes/production` directories are missing a file: `secrets.yaml`. To maintain security, we avoid storing this file in Git. Instead, we generate it dynamically when needed using the `kubernetes/generateSecretsFile.sh` script, which relies on environment variables to create a valid Kubernetes secrets file. It's worth noting that this script only outputs the file's contents without actually writing it.

Here are the steps for the staging environment, which also apply to production:
1. Copy the `kubernetes/staging/env.example` file to `kubernetes/staging/env`.
2. Fill in the required data in the `env` file.
3. Run the following command from the root directory:
  ```bash
  env $(cat kubernetes/staging/env) bash -c "kubernetes/generateSecretsFile.sh" > kubernetes/staging/secrets.yaml
  ```
This command generates the `secrets.yaml` file based on the provided environment variables and saves it in the staging directory.
