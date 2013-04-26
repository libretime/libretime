            if bitrate == 24 then 
                if stereo then
                    ignore(output_stereo(%mp3(bitrate = 24, stereo = true), !source))
                else
                    ignore(output_mono(%mp3(bitrate = 24, stereo = false), mean(!source)))
                end
            elsif bitrate == 32 then
                if stereo then
                    ignore(output_stereo(%mp3(bitrate = 32, stereo = true), !source))
                else
                    ignore(output_mono(%mp3(bitrate = 32, stereo = false), mean(!source)))
                end
            elsif bitrate == 48 then
                if stereo then
                    ignore(output_stereo(%mp3(bitrate = 48, stereo = true), !source))
                else
                    ignore(output_mono(%mp3(bitrate = 48, stereo = false), mean(!source)))
                end
            elsif bitrate == 64 then
                if stereo then
                    ignore(output_stereo(%mp3(bitrate = 64, stereo = true), !source))
                else
                    ignore(output_mono(%mp3(bitrate = 64, stereo = false), mean(!source)))
                end
            elsif bitrate == 96 then
                if stereo then
                    ignore(output_stereo(%mp3(bitrate = 96, stereo = true), !source))
                else
                    ignore(output_mono(%mp3(bitrate = 96, stereo = false), mean(!source)))
                end
            elsif bitrate == 128 then
                if stereo then
                    ignore(output_stereo(%mp3(bitrate = 128, stereo = true), !source))
                else
                    ignore(output_mono(%mp3(bitrate = 128, stereo = false), mean(!source)))
                end
            elsif bitrate == 160 then
                if stereo then
                    ignore(output_stereo(%mp3(bitrate = 160, stereo = true), !source))
                else
                    ignore(output_mono(%mp3(bitrate = 160, stereo = false), mean(!source)))
                end
            elsif bitrate == 192 then
                if stereo then
                    ignore(output_stereo(%mp3(bitrate = 192, stereo = true), !source))
                else
                    ignore(output_mono(%mp3(bitrate = 192, stereo = false), mean(!source)))
                end
            elsif bitrate == 224 then
                if stereo then
                    ignore(output_stereo(%mp3(bitrate = 224, stereo = true), !source))
                else
                    ignore(output_mono(%mp3(bitrate = 224, stereo = false), mean(!source)))
                end
            elsif bitrate == 256 then
                if stereo then
                    ignore(output_stereo(%mp3(bitrate = 256, stereo = true), !source))
                else
                    ignore(output_mono(%mp3(bitrate = 256, stereo = false), mean(!source)))
                end
            elsif bitrate == 320 then
                if stereo then
                    ignore(output_stereo(%mp3(bitrate = 320, stereo = true), !source))
                else
                    ignore(output_mono(%mp3(bitrate = 320, stereo = false), mean(!source)))
                end
            end

