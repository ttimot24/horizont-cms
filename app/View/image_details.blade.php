    <?php
    
    try {
        $type = rescue(fn() => 'image', 'image');
    
        if ($type === 'image') {
            $size = getimagesize($image);
            $image_size = $size[3];
            $image_size_width = explode("width=\"", $image_size);
            $image_size_width = explode("\"", $image_size_width[1]);
    
            $image_size_height = explode("height=\"", $image_size);
            $image_size_height = explode("\"", $image_size_height[1]);
    
            $display_type = 'image';
    
            $thumbnail = str_replace(basename($image), 'thumbs/' . basename($image), $image);
        } elseif ($type === 'video') {
            $image_size_width[0] = 'N/A';
            $image_size_height[0] = 'N/A';
            $size['mime'] = mime_content_type($image);
    
            $display_type = 'video';
        }
    } catch (\Exception $e) {
        $image = '';
        $image_size_width[0] = 'N/A';
        $image_size_height[0] = 'N/A';
        $size['mime'] = 'unknown';
        $display_type = 'unknown';
        $thumbnail = '';
    }
    
    ?>

    <div id="modal-xl-{{ $modal_id }}" class="modal {{ $modal_id }}-modal-xl" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <section class="row">
                        <div class='col-5'>
                            @if ($display_type === 'image')
                                <a href='{{ $image }}' target='_blank'>
                                    <img src='{{ $image }}' class='img img-thumbnail w-100'
                                        style='max-height:500px;'>
                                </a>
                            @elseif($display_type === 'video')
                                <video controls class="w-100" style="max-height:500px;">
                                    <source src="{{ $image }}" type="{{ $size['mime'] }}">
                                    Your browser does not support the video tag.
                                </video>
                            @else
                                <p>Unsupported media type.</p>
                            @endif

                            @if ($thumbnail)
                                <div class="row">
                                    <div class="col">
                                        <img src='{{ $thumbnail }}' class='img img-thumbnail'
                                            style='max-height:100px; margin-top:10px;cursor: pointer;'
                                            onclick="window.open('{{ $image }}', '_blank');" title='Download'>
                                    </div>
                                </div>
                            @endif

                        </div>
                        <div class='col-7' valign='top'>
                            <div class="card">
                                <div class="card-header">
                                    <h5 style="margin-top: .5rem !important; margin-bottom: .5rem !important;">Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <h4>File name</h4> {{ basename($image) }}
                                    <h4>Path</h4> <a href='{{ $image }}' target='_blank'>{{ $image }}</a>
                                    <h4>Size</h4> {{ $image_size_width[0] . 'X' . $image_size_height[0] }}
                                    <h4>Extension</h4> {{ $size['mime'] }}
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
