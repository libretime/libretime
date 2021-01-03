            if not icecast_vorbis_metadata then
                source := add(normalize=false, [amplify(0.00001, noise()), !source])
            end

            if bitrate == 24 or bitrate == 32 or bitrate == 48 then 
                if stereo then
                    ignore(output_stereo(%vorbis(quality=-0.1, channels = 2), !source))
                else
                    ignore(output_mono(%vorbis(quality=-0.1, channels = 1), mean(!source)))
                end
            elsif bitrate == 64 then
                if stereo then
                    ignore(output_stereo(%vorbis(quality=0, channels = 2), !source))
                else
                    ignore(output_mono(%vorbis(quality=0, channels = 1), mean(!source)))
                end
            elsif bitrate == 96 then
                if stereo then
                    ignore(output_stereo(%vorbis(quality=0.2, channels = 2), !source))
                else
                    ignore(output_mono(%vorbis(quality=0.2, channels = 1), mean(!source)))
                end
            elsif bitrate == 128 then
                if stereo then
                    ignore(output_stereo(%vorbis(quality=0.4, channels = 2), !source))
                else
                    ignore(output_mono(%vorbis(quality=0.4, channels = 1), mean(!source)))
                end
            elsif bitrate == 160 then
                if stereo then
                    ignore(output_stereo(%vorbis(quality=0.5, channels = 2), !source))
                else
                    ignore(output_mono(%vorbis(quality=0.5, channels = 1), mean(!source)))
                end
            elsif bitrate == 192 then
                if stereo then
                    ignore(output_stereo(%vorbis(quality=0.6, channels = 2), !source))
                else
                    ignore(output_mono(%vorbis(quality=0.6, channels = 1), mean(!source)))
                end
            elsif bitrate == 224 then
                if stereo then
                    ignore(output_stereo(%vorbis(quality=0.7, channels = 2), !source))
                else
                    ignore(output_mono(%vorbis(quality=0.7, channels = 1), mean(!source)))
                end
            elsif bitrate == 256 then
                if stereo then
                    ignore(output_stereo(%vorbis(quality=0.8, channels = 2), !source))
                else
                    ignore(output_mono(%vorbis(quality=0.8, channels = 1), mean(!source)))
                end
            elsif bitrate == 320 then
                if stereo then
                    ignore(output_stereo(%vorbis(quality=0.9, channels = 2), !source))
                else
                    ignore(output_mono(%vorbis(quality=0.9, channels = 1), mean(!source)))
                end
            end

