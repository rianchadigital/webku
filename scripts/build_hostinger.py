#!/usr/bin/env python3
import os
import shutil
import zipfile

def main():
    root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    dist_dir = os.path.join(root, 'dist')
    public_dir = os.path.join(root, 'public')
    assets_dir = os.path.join(root, 'assets')
    public_assets_dir = os.path.join(public_dir, 'assets')

    os.makedirs(assets_dir, exist_ok=True)
    os.makedirs(public_assets_dir, exist_ok=True)

    # 1. Copy app.js & app.css to assets/ and public/assets/
    dist_assets = os.path.join(dist_dir, 'assets')
    if os.path.exists(dist_assets):
        for f in ['app.js', 'app.css']:
            src = os.path.join(dist_assets, f)
            if os.path.exists(src):
                shutil.copy2(src, os.path.join(assets_dir, f))
                shutil.copy2(src, os.path.join(public_assets_dir, f))

    # 2. Copy api to dist/api
    api_src = os.path.join(root, 'api')
    dist_api = os.path.join(dist_dir, 'api')
    if os.path.exists(api_src):
        os.makedirs(dist_api, exist_ok=True)
        for item in os.listdir(api_src):
            s = os.path.join(api_src, item)
            d = os.path.join(dist_api, item)
            if os.path.isfile(s):
                shutil.copy2(s, d)

    # 3. Copy asset to dist/asset
    asset_src = os.path.join(root, 'asset')
    dist_asset = os.path.join(dist_dir, 'asset')
    if os.path.exists(asset_src):
        os.makedirs(dist_asset, exist_ok=True)
        shutil.copytree(asset_src, dist_asset, dirs_exist_ok=True)

    # 3b. Copy database to dist/database
    db_src = os.path.join(root, 'database')
    dist_db = os.path.join(dist_dir, 'database')
    if os.path.exists(db_src):
        os.makedirs(dist_db, exist_ok=True)
        shutil.copytree(db_src, dist_db, dirs_exist_ok=True)

    # 4. Copy .htaccess to dist/.htaccess and public/.htaccess
    htaccess_src = os.path.join(root, '.htaccess')
    if os.path.exists(htaccess_src):
        shutil.copy2(htaccess_src, os.path.join(dist_dir, '.htaccess'))
        shutil.copy2(htaccess_src, os.path.join(public_dir, '.htaccess'))

    # 5. Remove any old zip files
    pub_zip = os.path.join(public_dir, 'dist.zip')
    dist_zip = os.path.join(dist_dir, 'dist.zip')
    if os.path.exists(pub_zip):
        os.remove(pub_zip)
    if os.path.exists(dist_zip):
        os.remove(dist_zip)

    # 6. Create clean dist.zip
    with zipfile.ZipFile(pub_zip, 'w', zipfile.ZIP_DEFLATED) as z:
        for r, dirs, files in os.walk(dist_dir):
            for file in files:
                if file.endswith('.zip'):
                    continue
                full_path = os.path.join(r, file)
                rel_path = os.path.relpath(full_path, dist_dir)
                z.write(full_path, rel_path)

    # Copy to dist/dist.zip so direct link works either way
    shutil.copy2(pub_zip, dist_zip)
    size_mb = os.path.getsize(pub_zip) / (1024 * 1024)
    print(f"dist.zip created successfully: {size_mb:.2f} MB")

if __name__ == '__main__':
    main()
