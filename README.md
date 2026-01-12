<p align="center"><img src="./resources/logo.png" height="200"></p>

<h1 align="center">HorizontCMS</h1>

[![Laravel 11](https://img.shields.io/badge/Laravel-11-orange.svg)](http://laravel.com)
[![Bootstrap 5.3](https://img.shields.io/badge/Bootstrap-5.3-563d7c.svg)](http://getbootstrap.com)
[![VueJs 2.6](https://img.shields.io/badge/VueJs-2.6-green.svg)](http://vuejs.org)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/d645b6be9b6a42a8b6189cc32ea8f546)](https://www.codacy.com/app/ttimot24/HorizontCMS?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ttimot24/HorizontCMS&amp;utm_campaign=Badge_Grade)

**HorizontCMS** is an open-source, responsive Content Management System (CMS) built on the powerful combination of Laravel 11, Vue.js 2.6, and Bootstrap 5.3. With its sleek design and robust features, HorizontCMS empowers developers like you to create stunning websites and blogs for the next generation.

This lightweight CMS platform is not just about functionality—it's about empowerment. Whether you're a seasoned developer or a novice user, HorizontCMS provides intuitive tools and a seamless interface to extend and build your online presence with ease. With just one click, you can unlock endless possibilities and unleash your creativity.


### Latest version: [v1.4.2](https://github.com/ttimot24/HorizontCMS/releases/tag/v1.4.2)


### Installation
#### Browser

After downloading and copying the files to the server, navigate to the app root folder and run ```composer install```. Then head to your domain. HorizontCMS can recognize if not installed yet, and redirects you to the installer. Follow the instructions, add the required credentials and you're done.

#### Console 

Experience seamless installation and setup with our Command Line Interface (CLI) installer. Designed to streamline the installation process, our CLI installer makes easy to get up and running with HorizontCMS in just a few simple steps. 

  1. Download or clone the CMS
  2. Navigate to the app root folder and run ```composer install```
  3. Run ```php artisan hcms:install```
  4. Enter the required database and administrator informations.
  5. You're finished.

The no-prompt makes possible to fully automate installations without any user prompts, enabling seamless integration into your development pipeline.

#### Install manually from scratch

  [Website For Students Tutorial](https://websiteforstudents.com/how-to-install-horizontcms-on-ubuntu-18-04-16-04-with-apache2/)

#### Docker Image
Every release is published to DockerHub.

https://hub.docker.com/repository/docker/ttimot24/horizont-cms  

#### Docker Compose

Build and run HorizontCMS locally. 

```bash
docker-compose build && docker-compose up -d
```

#### Kubernetes
```bash
kubectl create deployment horizont-cms --image=ttimot24/horizont-cms:latest
```
  
### Sample plugin

  [GoogleMaps](https://github.com/ttimot24/GoogleMaps)

### Angular Client Library

[https://www.npmjs.com/package/@smartnowx/hcms-commons](https://www.npmjs.com/package/@smartnowx/hcms-commons)

### Contribution

Looking to get involved and make a difference? You've come to the right place! Here are some ways you can contribute to our project:

1. **Create a Theme or Plugin:** Are you a creative designer or a skilled developer? Contribute by creating a new theme or plugin to enhance the functionality and aesthetics of our project.

2. **Create Translations:** Help us make our project accessible to a global audience by creating translations in different languages. Your efforts will ensure that more people can benefit from our work.

3. **Help in the Docs:** Documentation is key to helping developers understand our project. If you have a knack for explaining things clearly, consider lending a hand in improving our documentation to make it more comprehensive and user-friendly.

4. **Write Tests:** Ensure the stability and reliability of our project by writing tests. Your contributions will help catch bugs early and maintain the quality of our codebase.

5. **Report Issues, Send Pull Requests:** If you encounter any bugs or have ideas for improvements, don't hesitate to report them! And if you're feeling adventurous, why not send us a pull request with your proposed fixes or enhancements?

6. **Tell Us What You Think:** Your feedback is invaluable to us. Whether you love our project or have suggestions for improvement, we want to hear from you! Share your thoughts, ideas, and suggestions with us so we can continue to grow and improve.

Every contribution, big or small, makes a difference. Thank you for considering contributing to our project—we couldn't do it without amazing contributors like you! 🚀

### Donation
If this project makes your website development easier, you can buy me a coffe. :)

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/donate?hosted_button_id=73CV4FU4TNM3U)

##

Project by Timot Tarjani [(@ttimot24)](https://github.com/ttimot24)
