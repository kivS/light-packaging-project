
## TODO
- [harden php file upload](https://dev.to/einlinuus/how-to-upload-files-with-php-correctly-and-securely-1kng)


## Dependencies

- [QR code generator](https://github.com/davidshimjs/qrcodejs)

## Development dependencies
-[Tailwind CSS standalone binary](https://github.com/tailwindlabs/tailwindcss/releases)




## Instructions

- *For vscode tailwind completions only*
```bash
# Install tailwindcss with all the plugins we're using
npm install --no-save --no-package-lock tailwindcss  @tailwindcss/forms
```

- Start tailwindcss standalone binary after installing it in the root of the project.
```bash
# for dashboard css (dev)
./tailwind -c dashboard/tailwind.config.js  -i dashboard/tailwind.input.css -o public/assets/dashboard.css --watch

# for front-end pages css (dev)
./tailwind -c front-end-pages/tailwind.config.js  -i front-end-pages/tailwind.input.css -o public/assets/app.css --watch     
```