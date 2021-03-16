<!----- CUSTOMERS-REVIEWS-SECTION-START ------>
    <section class="cutomer-reviews-bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-12">
                    <div class="main-heading">
                        <h3><span class="pink-clr">Customer </span>Reviews</h3>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12 col-md-12 col-lg-10">
                    <ul class="list-unstyled reviews-bg mb-2">
                        <li class="d-inline-block">
                            <section class='rating-widget'>
                                <div class='rating-stars'>
                                    {!! commonHelper::ratingReview($product->product_id) !!}
                                </div>
                        </section>
                        </li>
                        <li class="d-inline-block">{{$total_reviews}} Reviews</li>
                    </ul> 
                    <div class="progressbar-wrapper">
                        <div class="row">
                            <div class="col-4 col-sm-3 col-md-3">
                                <span class="reviews-text">Excellent</span>
                            </div>
                            <div class="col-6 col-sm-8 col-md-8">
                                <div class="d-inline-block progress-bar-width">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{$avg_rating_count5}}" aria-valuemin="0" aria-valuemax="{{$avg_rating_count5}}">{{$avg_rating_count5}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1 col-md-1">
                                <span>5</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4 col-sm-3 col-md-3">
                                <span class="reviews-text">Good</span>
                            </div>
                            <div class="col-6 col-sm-8 col-md-8">
                                <div class="d-inline-block progress-bar-width">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-success2" role="progressbar" aria-valuenow="{{$avg_rating_count4}}" aria-valuemin="0" aria-valuemax="{{$avg_rating_count4}}">{{$avg_rating_count4}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1 col-md-1">
                                <span>4</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4 col-sm-3 col-md-3">
                                <span class="reviews-text">Average</span>
                            </div>
                            <div class="col-6 col-sm-8 col-md-8">
                                <div class="d-inline-block progress-bar-width">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="{{$avg_rating_count3}}" aria-valuemin="0" aria-valuemax="{{$avg_rating_count3}}">{{$avg_rating_count3}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1 col-md-1">
                                <span>3</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4 col-sm-3 col-md-3">
                                <span class="reviews-text">Below Average</span>
                            </div>
                            <div class="col-6 col-sm-8 col-md-8">
                                <div class="d-inline-block progress-bar-width">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-warning2" role="progressbar" aria-valuenow="{{$avg_rating_count2}}" aria-valuemin="0" aria-valuemax="{{$avg_rating_count2}}">{{$avg_rating_count2}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1 col-md-1">
                                <span>2</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4 col-sm-3 col-md-3">
                                <span class="reviews-text">Poor</span>
                            </div>
                            <div class="col-6 col-sm-8 col-md-8">
                                <div class="d-inline-block progress-bar-width">
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-dark" role="progressbar" aria-valuenow="{{$avg_rating_count1}}" aria-valuemin="0" aria-valuemax="{{$avg_rating_count1}}">{{$avg_rating_count1}}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1 col-md-1">
                                <span>1</span>
                            </div>
                        </div>
                        <div class="revie-btn mt-4">
                            @if($total_reviews > 0)
                                <a href="{{url('products/reviews/'.$product->product_id)}}" style="padding: 10px;" class="pink-btn">View all Reviews</a>
                            @endif
                            @if(session('user_id') != '')
                                <a href="{{url('write/review/'.$product->product_id)}}" class="black-btn">Write  a Review</a>
                            @endif
                        </div> 
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!----- CUSTOMERS-REVIEWS-SECTION-END ------>