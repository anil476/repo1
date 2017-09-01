@extends('layouts.master')

@section('title')
    GO-Fuel
@endsection
@section('content')
@include('includes.header')
    <header>
        <div class="container">
            <div class="row">
                <div class="col-sm-7">
                    <div class="header-content">
                        <div class="header-content-inner">
                            <h1>Easiest way to know fuel Price in your City.This App helps you to know the daily updated petrol/diesel price.Best utility app for India</h1>
                            <a href="#download" class="btn btn-outline btn-xl page-scroll">Download Now!</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="device-container">
                        <div class="device-mockup iphone6_plus portrait white">
                            <div class="device" style="height:76%;">
                                <div class="screen">
                                    <!-- Demo image for screen mockup, you can put an image here, some HTML, an animation, video, or anything else! -->
                                    <img src="assets/img/demo-screen-1.png" class="img-responsive" alt="">
                                </div>
                                <div class="button">
                                    <!-- You can hook the "home button" to some JavaScript events or just remove it -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="download" class="download bg-primary text-center">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <h2 class="section-heading">Discover what all the buzz is about!</h2>
                    <p>Our app is available on any mobile device! Download now to get started!</p>
                    <div class="badges">
                        <a class="badge-link" href="#"><img src="assets/img/google-play-badge.svg" alt=""></a>
                        <a class="badge-link" href="#"><img src="assets/img/app-store-badge.svg" alt=""></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="features">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="section-heading">
                        <h2>APP Features</h2>
                        <p class="text-muted">Check out what you can do with this app!</p>
                        <hr>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="device-container">
                        <div class="device-mockup iphone6_plus portrait white">
                            <div class="device">
                                <div class="screen">
                                    <!-- Demo image for screen mockup, you can put an image here, some HTML, an animation, video, or anything else! -->
                                    <img src="assets/img/demo-screen-1.png" class="img-responsive" alt=""> </div>
                                <div class="button">
                                    <!-- You can hook the "home button" to some JavaScript events or just remove it -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fa fa-cube" aria-hidden="true"></i>
                                    <h3>Fuel Filling</h3>
                                    <p class="text-muted">Keep fuel records of your vehicle's,that will hepls you to improve your vehicle mileage!</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fa fa-server" aria-hidden="true"></i>
                                    <h3>Average</h3>
                                    <p class="text-muted">Automatic average will be generated as per your fuel filled details,that will helps you to increase your vehicle average!</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fa fa-cubes" aria-hidden="true"></i>
                                    <h3>Servicing</h3>
                                    <p class="text-muted">Keep your vehicle's servicing records.It will notify about your next vehicle servicing date and also suggest nearest place to service your vehicle!</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fa fa-map" aria-hidden="true"></i>
                                    <h3>Insurance</h3>
                                    <p class="text-muted">Easily manage your vehicle insurance policy.It will notify about your renewal insurance date and also suggest you nearest place to renew your policy!</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="cta-content">
            <div class="container">
                <h2>Stop waiting.<br>Start now.</h2>
                <a href="#contact" class="btn btn-outline btn-xl page-scroll">Let's Get Started!</a>
            </div>
        </div>
        <div class="overlay"></div>
    </section>

    <section id="contact" class="contact bg-primary">
        <div class="container">
            <h2>We <i class="fa fa-heart"></i> new friends!</h2>
            <ul class="list-inline list-social">
                <li class="social-twitter">
                    <a href="#"><i class="fa fa-twitter"></i></a>
                </li>
                <li class="social-facebook">
                    <a href="#"><i class="fa fa-facebook"></i></a>
                </li>
                <li class="social-google-plus">
                    <a href="#"><i class="fa fa-google-plus"></i></a>
                </li>
            </ul>
        </div>
    </section>
    @include('includes.footer')
@endsection
